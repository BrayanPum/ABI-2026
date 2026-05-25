<?php

namespace App\Http\Controllers\Formats;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Formats\Concerns\GeneratesPdf;
use App\Models\FormatoCampo;
use App\Models\FormatoRegistro;
use App\Models\FormatoTipo;
use App\Models\FormatoValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FormatoRegistroController extends Controller
{
    use GeneratesPdf;

    public function index(FormatoTipo $tipo)
    {
        $this->checkAccess($tipo);

        $query = FormatoRegistro::where('formato_tipo_id', $tipo->id)->latest();

        if (auth()->user()->role !== 'research_staff') {
            $query->where('user_id', Auth::id());
        }

        $registros = $query->with('user')->paginate(10);

        return view('formats.registros.index', compact('tipo', 'registros'));
    }

    public function create(FormatoTipo $tipo)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');

        return view('formats.registros.create', compact('tipo'));
    }

    public function store(Request $request, FormatoTipo $tipo)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');

        $request->validate($this->buildRules($tipo));

        $registro = FormatoRegistro::create([
            'formato_tipo_id' => $tipo->id,
            'user_id'         => Auth::id(),
        ]);

        $this->saveValores($registro, $tipo, $request);

        return redirect()->route('formatos.registros.index', $tipo)
            ->with('success', 'Registro creado correctamente.');
    }

    public function show(FormatoTipo $tipo, FormatoRegistro $registro)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');
        $valores = $registro->valores->pluck('valor', 'formato_campo_id')->toArray();

        return view('formats.registros.show', compact('tipo', 'registro', 'valores'));
    }

    public function edit(FormatoTipo $tipo, FormatoRegistro $registro)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');
        $valores = $registro->valores->pluck('valor', 'formato_campo_id')->toArray();

        return view('formats.registros.edit', compact('tipo', 'registro', 'valores'));
    }

    public function update(Request $request, FormatoTipo $tipo, FormatoRegistro $registro)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');

        $request->validate($this->buildRules($tipo));

        foreach ($tipo->campos as $campo) {
            $valor = $campo->tipo === 'checkbox'
                ? ($request->has('campo_' . $campo->id) ? '1' : '0')
                : $request->input('campo_' . $campo->id);

            $registro->valores()->updateOrCreate(
                ['formato_campo_id' => $campo->id],
                ['valor' => $valor]
            );
        }

        return redirect()->route('formatos.registros.show', [$tipo, $registro])
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(FormatoTipo $tipo, FormatoRegistro $registro)
    {
        $this->checkAccess($tipo);

        $registro->delete();

        return redirect()->route('formatos.registros.index', $tipo)
            ->with('success', 'Registro eliminado.');
    }

    public function exportPdf(FormatoTipo $tipo, FormatoRegistro $registro)
    {
        $this->checkAccess($tipo);

        $tipo->load('campos');
        $valores = $registro->valores->pluck('valor', 'formato_campo_id')->toArray();

        return $this->generarPdf(
            'formats.registros.pdf',
            compact('tipo', 'registro', 'valores'),
            Str::slug($tipo->nombre) . '_' . $registro->id . '.pdf'
        );
    }

    private function checkAccess(FormatoTipo $tipo): void
    {
        if (! $tipo->esAccesiblePor(auth()->user()->role)) {
            abort(403);
        }
    }

    private function buildRules(FormatoTipo $tipo): array
    {
        $rules = [];

        foreach ($tipo->campos as $campo) {
            if ($campo->tipo === 'checkbox') continue;

            $rule = $campo->requerido ? 'required' : 'nullable';

            $rules['campo_' . $campo->id] = match ($campo->tipo) {
                'numero' => $rule . '|numeric',
                'fecha'  => $rule . '|date',
                default  => $rule . '|string|max:5000',
            };
        }

        return $rules;
    }

    private function saveValores(FormatoRegistro $registro, FormatoTipo $tipo, Request $request): void
    {
        foreach ($tipo->campos as $campo) {
            $valor = $campo->tipo === 'checkbox'
                ? ($request->has('campo_' . $campo->id) ? '1' : '0')
                : $request->input('campo_' . $campo->id);

            FormatoValor::create([
                'formato_registro_id' => $registro->id,
                'formato_campo_id'    => $campo->id,
                'valor'               => $valor,
            ]);
        }
    }
}
