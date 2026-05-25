<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffCity;
use App\Models\ResearchStaff\ResearchStaffDepartment;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $query = array_filter([
            'selected_department_id' => $request->filled('department_id') ? $request->integer('department_id') : null,
            'city_search' => $request->filled('search') ? $request->string('search')->toString() : null,
            'cities_per_page' => $this->resolvePerPage($request->get('per_page', 10)) !== 10
                ? $this->resolvePerPage($request->get('per_page', 10))
                : null,
        ]);

        return redirect()->route('departments-cities.index', $query);
    }

    public function create(Request $request): View
    {
        return view('city.create', [
            'city' => new ResearchStaffCity([
                'department_id' => $request->integer('department_id') ?: null,
            ]),
            'departments' => ResearchStaffDepartment::orderBy('name')->pluck('name', 'id'),
            'redirectTo' => $this->resolveRedirectTarget($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name',
            'department_id' => 'required|exists:departments,id',
        ]);

        $city = ResearchStaffCity::create($data);

        return $this->redirectToTarget($request, 'cities.index')
            ->with('success', "Ciudad '{$city->name}' creada correctamente.");
    }

    public function show(Request $request, ResearchStaffCity $city): RedirectResponse
    {
        return redirect()->route('departments-cities.index', [
            'selected_department_id' => $city->department_id,
        ] + ($request->filled('city_search')
            ? ['city_search' => $request->string('city_search')->toString()]
            : []));
    }

    public function edit(Request $request, ResearchStaffCity $city): View
    {
        return view('city.edit', [
            'city' => $city,
            'departments' => ResearchStaffDepartment::orderBy('name')->pluck('name', 'id'),
            'redirectTo' => $this->resolveRedirectTarget($request),
        ]);
    }

    public function update(Request $request, ResearchStaffCity $city): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name,' . $city->id,
            'department_id' => 'required|exists:departments,id',
        ]);

        $city->update($data);

        return $this->redirectToTarget($request, 'cities.index')
            ->with('success', "Ciudad '{$city->name}' actualizada correctamente.");
    }

    public function destroy(Request $request, ResearchStaffCity $city): RedirectResponse
    {
        try {
            $name = $city->name;
            $city->delete();

            return $this->redirectToTarget($request, 'cities.index')
                ->with('success', "Ciudad '{$name}' eliminada correctamente.");
        } catch (QueryException $exception) {
            return $this->redirectToTarget($request, 'cities.index')
                ->with('error', 'No se puede eliminar la ciudad porque tiene informacion relacionada.');
        }
    }

    public function byDepartment(ResearchStaffDepartment $department): JsonResponse
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
