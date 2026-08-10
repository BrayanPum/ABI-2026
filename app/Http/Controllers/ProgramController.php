<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffCity;
use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $search = $request->get('search');
            $researchGroupId = $request->get('research_group_id');
            $perPage = (int) $request->get('per_page', 10);
            $perPage = $perPage > 0 ? min($perPage, 100) : 10;

            $programs = ResearchStaffProgram::query()
                ->with([
                    'researchGroup',
                    'cities' => fn ($query) => $query->orderBy('name'),
                ])
                ->withCount('cities')
                ->when($search, function ($query, string $search) {
                    $query->where(function ($nestedQuery) use ($search) {
                        $nestedQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                })
                ->when($researchGroupId, function ($query, $groupId) {
                    $query->where('research_group_id', $groupId);
                })
                ->orderBy('name')
                ->paginate($perPage)
                ->appends($request->query());

            $researchGroups = ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id');

            return view('programs.index', [
                'programs' => $programs,
                'researchGroups' => $researchGroups,
                'search' => $search,
                'researchGroupId' => $researchGroupId,
                'perPage' => $perPage,
            ]);
        } catch (\Exception $exception) {
            Log::error('Error al listar programas: ' . $exception->getMessage());

            return view('programs.index', [
                'programs' => collect(),
                'researchGroups' => collect(),
                'search' => '',
                'researchGroupId' => null,
                'perPage' => 10,
                'error' => 'Ocurrio un error al cargar los programas.',
            ]);
        }
    }

    public function create(): View
    {
        return view('programs.create', [
            'program' => new ResearchStaffProgram(),
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
            'cities' => ResearchStaffCity::with('department')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'code' => 'required|integer|min:1|unique:programs,code',
                'name' => 'required|string|max:100',
                'research_group_id' => 'required|exists:research_groups,id',
                'city_ids' => 'nullable|array',
                'city_ids.*' => 'integer|distinct|exists:cities,id',
            ], [
                'code.required' => 'El codigo del programa es obligatorio.',
                'code.integer' => 'El codigo debe ser un numero entero.',
                'code.min' => 'El codigo debe ser mayor a 0.',
                'code.unique' => 'Ya existe un programa con este codigo.',
                'name.required' => 'El nombre del programa es obligatorio.',
                'name.max' => 'El nombre no puede superar los 100 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigacion.',
                'research_group_id.exists' => 'El grupo de investigacion seleccionado no es valido.',
                'city_ids.array' => 'Las ciudades asociadas deben enviarse como una lista valida.',
                'city_ids.*.integer' => 'Cada ciudad seleccionada debe ser valida.',
                'city_ids.*.distinct' => 'No puedes asociar la misma ciudad mas de una vez.',
                'city_ids.*.exists' => 'Una de las ciudades seleccionadas no existe o no esta disponible.',
            ]);

            $selectedCityIds = $this->normalizeCityIds($data['city_ids'] ?? []);
            unset($data['city_ids']);

            return DB::transaction(function () use ($data, $selectedCityIds) {
                $program = ResearchStaffProgram::create($data);
                $program->cities()->sync($selectedCityIds);

                Log::info('Programa creado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'research_group_id' => $program->research_group_id,
                    'city_ids' => $selectedCityIds,
                    'user_id' => auth()->id(),
                ]);

                $cityMessage = count($selectedCityIds) > 0
                    ? ' con ' . count($selectedCityIds) . ' ciudades asociadas'
                    : ' sin ciudades asociadas';

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$program->name}' creado correctamente{$cityMessage}.");
            });
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return redirect()->back()
                ->withErrors($exception->validator)
                ->withInput();
        } catch (\Exception $exception) {
            Log::error('Error al crear programa: ' . $exception->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrio un error al crear el programa.')
                ->withInput();
        }
    }

    public function show(ResearchStaffProgram $program): View
    {
        if ($program->trashed()) {
            abort(404, 'El programa no esta disponible.');
        }

        $program->load([
            'researchGroup',
            'cities' => fn ($query) => $query->with('department')->orderBy('name'),
        ]);

        return view('programs.show', compact('program'));
    }

    public function edit(ResearchStaffProgram $program): View
    {
        if ($program->trashed()) {
            abort(404, 'El programa no esta disponible.');
        }

        $program->load([
            'cities' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('programs.edit', [
            'program' => $program,
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
            'cities' => ResearchStaffCity::with('department')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ResearchStaffProgram $program): RedirectResponse
    {
        try {
            $data = $request->validate([
                'code' => 'required|integer|min:1|unique:programs,code,' . $program->id,
                'name' => 'required|string|max:100',
                'research_group_id' => 'required|exists:research_groups,id',
                'city_ids' => 'nullable|array',
                'city_ids.*' => 'integer|distinct|exists:cities,id',
            ], [
                'code.required' => 'El codigo del programa es obligatorio.',
                'code.integer' => 'El codigo debe ser un numero entero.',
                'code.min' => 'El codigo debe ser mayor a 0.',
                'code.unique' => 'Ya existe otro programa con este codigo.',
                'name.required' => 'El nombre del programa es obligatorio.',
                'name.max' => 'El nombre no puede superar los 100 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigacion.',
                'research_group_id.exists' => 'El grupo de investigacion seleccionado no es valido.',
                'city_ids.array' => 'Las ciudades asociadas deben enviarse como una lista valida.',
                'city_ids.*.integer' => 'Cada ciudad seleccionada debe ser valida.',
                'city_ids.*.distinct' => 'No puedes asociar la misma ciudad mas de una vez.',
                'city_ids.*.exists' => 'Una de las ciudades seleccionadas no existe o no esta disponible.',
            ]);

            $selectedCityIds = $this->normalizeCityIds($data['city_ids'] ?? []);
            unset($data['city_ids']);

            return DB::transaction(function () use ($program, $data, $selectedCityIds) {
                if ($program->trashed()) {
                    return redirect()
                        ->route('programs.index')
                        ->with('error', 'No se puede actualizar un programa eliminado.');
                }

                $program->update($data);
                $program->cities()->sync($selectedCityIds);

                Log::info('Programa actualizado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'city_ids' => $selectedCityIds,
                    'user_id' => auth()->id(),
                ]);

                $cityMessage = count($selectedCityIds) > 0
                    ? ' y ' . count($selectedCityIds) . ' ciudades asociadas'
                    : ' y sin ciudades asociadas';

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$program->name}' actualizado correctamente{$cityMessage}.");
            });
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return redirect()->back()
                ->withErrors($exception->validator)
                ->withInput();
        } catch (\Exception $exception) {
            Log::error('Error al actualizar programa: ' . $exception->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrio un error al actualizar el programa.')
                ->withInput();
        }
    }

    public function destroy(ResearchStaffProgram $program): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($program) {
                if ($program->trashed()) {
                    return redirect()
                        ->route('programs.index')
                        ->with('error', 'El programa ya fue eliminado.');
                }

                $name = $program->name;
                $program->delete();

                Log::info('Programa eliminado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$name}' eliminado correctamente.");
            });
        } catch (QueryException $exception) {
            Log::error('Error de integridad al eliminar programa: ' . $exception->getMessage());

            return redirect()
                ->route('programs.index')
                ->with('error', 'No se puede eliminar el programa porque tiene informacion relacionada.');
        } catch (\Exception $exception) {
            Log::error('Error al eliminar programa: ' . $exception->getMessage());

            return redirect()
                ->route('programs.index')
                ->with('error', 'Ocurrio un error al eliminar el programa.');
        }
    }

    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                $program = ResearchStaffProgram::withTrashed()->findOrFail($id);

                if (! $program->trashed()) {
                    return redirect()
                        ->route('programs.index')
                        ->with('error', 'El programa no esta eliminado.');
                }

                $program->restore();

                Log::info('Programa restaurado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$program->name}' restaurado correctamente.");
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return redirect()
                ->route('programs.index')
                ->with('error', 'No se encontro el programa especificado.');
        } catch (\Exception $exception) {
            Log::error('Error al restaurar programa: ' . $exception->getMessage());

            return redirect()
                ->route('programs.index')
                ->with('error', 'Ocurrio un error al restaurar el programa.');
        }
    }

    /**
     * @param array<int, mixed> $cityIds
     * @return array<int, int>
     */
    private function normalizeCityIds(array $cityIds): array
    {
        return collect($cityIds)
            ->filter(fn ($cityId) => filled($cityId))
            ->map(fn ($cityId) => (int) $cityId)
            ->unique()
            ->values()
            ->all();
    }
}
