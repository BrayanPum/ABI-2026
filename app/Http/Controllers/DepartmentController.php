<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffDepartment;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $query = array_filter([
            'department_search' => $request->filled('search') ? $request->string('search')->toString() : null,
            'departments_per_page' => $this->resolvePerPage($request->get('per_page', 10)) !== 10
                ? $this->resolvePerPage($request->get('per_page', 10))
                : null,
        ]);

        return redirect()->route('departments-cities.index', $query);
    }

    public function unifiedIndex(Request $request): View
    {
        $departmentSearch = $request->string('department_search')->toString();
        $departmentPerPage = $this->resolvePerPage($request->get('departments_per_page', 10));
        $selectedDepartmentId = $request->integer('selected_department_id') ?: null;
        $citySearch = $request->string('city_search')->toString();
        $cityPerPage = $this->resolvePerPage($request->get('cities_per_page', 10));

        $departments = ResearchStaffDepartment::query()
            ->withCount('cities')
            ->when($departmentSearch !== '', function ($query) use ($departmentSearch) {
                $query->where('name', 'like', "%{$departmentSearch}%");
            })
            ->orderBy('name')
            ->paginate($departmentPerPage, ['*'], 'departments_page')
            ->appends($request->query());

        $selectedDepartment = $selectedDepartmentId
            ? ResearchStaffDepartment::query()->withCount('cities')->find($selectedDepartmentId)
            : null;

        $cities = null;

        if ($selectedDepartment) {
            $cities = $selectedDepartment->cities()
                ->with('department')
                ->when($citySearch !== '', function ($query) use ($citySearch) {
                    $query->where('name', 'like', "%{$citySearch}%");
                })
                ->orderBy('name')
                ->paginate($cityPerPage, ['*'], 'cities_page')
                ->appends($request->query());
        }

        return view('departments.unified-index', [
            'departments' => $departments,
            'departmentSearch' => $departmentSearch,
            'departmentPerPage' => $departmentPerPage,
            'selectedDepartment' => $selectedDepartment,
            'selectedDepartmentId' => $selectedDepartmentId,
            'cities' => $cities,
            'citySearch' => $citySearch,
            'cityPerPage' => $cityPerPage,
            'currentPath' => $request->getRequestUri(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('departments.create', [
            'department' => new ResearchStaffDepartment(),
            'redirectTo' => $this->resolveRedirectTarget($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
        ]);

        $department = ResearchStaffDepartment::create($data);

        return $this->redirectToTarget($request, 'departments.index')
            ->with('success', "Departamento '{$department->name}' creado correctamente.");
    }

    public function show(Request $request, ResearchStaffDepartment $department): RedirectResponse
    {
        return redirect()->route('departments-cities.index', [
            'selected_department_id' => $department->id,
        ]);
    }

    public function edit(Request $request, ResearchStaffDepartment $department): View
    {
        return view('departments.edit', [
            'department' => $department,
            'redirectTo' => $this->resolveRedirectTarget($request),
        ]);
    }

    public function update(Request $request, ResearchStaffDepartment $department): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
        ]);

        $department->update($data);

        return $this->redirectToTarget($request, 'departments.index')
            ->with('success', "Departamento '{$department->name}' actualizado correctamente.");
    }

    public function destroy(Request $request, ResearchStaffDepartment $department): RedirectResponse
    {
        try {
            $name = $department->name;
            $department->delete();

            return $this->redirectToTarget($request, 'departments.index')
                ->with('success', "Departamento '{$name}' eliminado correctamente.");
        } catch (QueryException $exception) {
            return $this->redirectToTarget($request, 'departments.index')
                ->with('error', 'No se puede eliminar el departamento porque tiene informacion relacionada.');
        }
    }

    public function cities(ResearchStaffDepartment $department): JsonResponse
    {
        $cities = $department->cities()
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($cities);
    }

    private function resolvePerPage(mixed $perPage, int $default = 10): int
    {
        $perPage = (int) $perPage;

        return $perPage > 0 ? min($perPage, 100) : $default;
    }

    private function resolveRedirectTarget(Request $request): ?string
    {
        $redirectTo = $request->input('redirect_to');

        return is_string($redirectTo) && str_starts_with($redirectTo, '/') ? $redirectTo : null;
    }

    private function redirectToTarget(Request $request, string $fallbackRoute): RedirectResponse
    {
        $redirectTo = $this->resolveRedirectTarget($request);

        return $redirectTo
            ? redirect($redirectTo)
            : redirect()->route($fallbackRoute);
    }
}
