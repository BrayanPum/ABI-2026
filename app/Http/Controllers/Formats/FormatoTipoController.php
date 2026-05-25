<?php

namespace App\Http\Controllers\Formats;

use App\Http\Controllers\Controller;
use App\Models\FormatoTipo;
use App\Models\FormatoCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormatoTipoController extends Controller
{
    public function hub()
    {
        $role = auth()->user()->role;

        $formatos = FormatoTipo::where('activo', true)
            ->get()
            ->filter(fn($f) => $f->esAccesiblePor($role))
            ->values();

        return view('formats.index', compact('formatos'));
    }

    public function index()
    {
        $tipos = FormatoTipo::latest()->paginate(15);

        return view('formats.builder.index', compact('tipos'));
    }

    public function create()
    {
        return view('formats.builder.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'codigo'          => 'nullable|string|max:50',
            'descripcion'     => 'nullable|string',
            'icono'           => 'nullable|string|max:100',
            'color'           => 'nullable|string|max:50',
            'roles_acceso'    => 'required|array|min:1',
            'roles_acceso.*'  => 'in:research_staff,professor,committee_leader,student',
            'campos'          => 'required|array|min:1',
            'campos.*.etiqueta' => 'required|string|max:255',
            'campos.*.tipo'     => 'required|in:texto,numero,fecha,hora,select,checkbox,textarea',
        ]);

        $tipo = FormatoTipo::create([
            'nombre'       => $request->nombre,
            'codigo'       => $request->codigo,
            'descripcion'  => $request->descripcion,
            'icono'        => $request->icono ?: 'ti ti-file-text',
            'color'        => $request->color ?: 'blue',
            'roles_acceso' => $request->roles_acceso,
            'activo'       => true,
        ]);

        $this->guardarCampos($tipo, $request->campos);

        return redirect()->route('formatos.tipos.show', $tipo)
            ->with('success', 'Formato creado correctamente.');
    }

    public function show(FormatoTipo $tipo)
    {
        $tipo->load('campos');

        return view('formats.builder.show', compact('tipo'));
    }

    public function edit(FormatoTipo $tipo)
    {
        $tipo->load('campos');

        return view('formats.builder.edit', compact('tipo'));
    }

    public function update(Request $request, FormatoTipo $tipo)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'codigo'          => 'nullable|string|max:50',
            'descripcion'     => 'nullable|string',
            'icono'           => 'nullable|string|max:100',
            'color'           => 'nullable|string|max:50',
            'roles_acceso'    => 'required|array|min:1',
            'roles_acceso.*'  => 'in:research_staff,professor,committee_leader,student',
            'campos'          => 'required|array|min:1',
            'campos.*.etiqueta' => 'required|string|max:255',
            'campos.*.tipo'     => 'required|in:texto,numero,fecha,hora,select,checkbox,textarea',
        ]);

        $tipo->update([
            'nombre'       => $request->nombre,
            'codigo'       => $request->codigo,
            'descripcion'  => $request->descripcion,
            'icono'        => $request->icono ?: 'ti ti-file-text',
            'color'        => $request->color ?: 'blue',
            'roles_acceso' => $request->roles_acceso,
        ]);

        $tipo->campos()->delete();
        $this->guardarCampos($tipo, $request->campos);

        return redirect()->route('formatos.tipos.show', $tipo)
            ->with('success', 'Formato actualizado correctamente.');
    }

    public function destroy(FormatoTipo $tipo)
    {
        $tipo->delete();

        return redirect()->route('formatos.tipos.index')
            ->with('success', 'Formato eliminado.');
    }

    private function guardarCampos(FormatoTipo $tipo, array $campos): void
    {
        foreach ($campos as $index => $data) {
            $opciones = null;

            if (($data['tipo'] ?? '') === 'select' && ! empty($data['opciones'])) {
                $opciones = [];
                foreach (explode("\n", $data['opciones']) as $linea) {
                    $linea = trim($linea);
                    if ($linea === '') continue;

                    if (str_contains($linea, '|')) {
                        [$valor, $etiqueta] = explode('|', $linea, 2);
                        $opciones[] = ['valor' => trim($valor), 'etiqueta' => trim($etiqueta)];
                    } else {
                        $opciones[] = ['valor' => Str::slug($linea, '_'), 'etiqueta' => $linea];
                    }
                }
            }

            FormatoCampo::create([
                'formato_tipo_id' => $tipo->id,
                'nombre'          => Str::slug($data['etiqueta'], '_'),
                'etiqueta'        => $data['etiqueta'],
                'tipo'            => $data['tipo'],
                'opciones'        => $opciones,
                'requerido'       => isset($data['requerido']),
                'seccion'         => $data['seccion'] ?? null,
                'orden'           => $index,
            ]);
        }
    }
}
