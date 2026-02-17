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
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $departmentId = $request->get('department_id');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $cities = ResearchStaffCity::query()
            ->with('department')
            ->when($search, function ($query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($departmentId, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        $departments = ResearchStaffDepartment::orderBy('name')->pluck('name', 'id');

        return view('city.index', [
            'cities' => $cities,
            'departments' => $departments,
            'search' => $search,
            'departmentId' => $departmentId,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('city.create', [
            'city' => new ResearchStaffCity(),
            'departments' => ResearchStaffDepartment::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name',
            'department_id' => 'required|exists:departments,id',
        ]);

        $city = ResearchStaffCity::create($data);

        return redirect()
            ->route('cities.index')
            ->with('success', "Ciudad '{$city->name}' creada correctamente.");
    }

    public function show(ResearchStaffCity $city): View
    {
        $city->load('department');

        return view('city.show', compact('city'));
    }

    public function edit(ResearchStaffCity $city): View
    {
        return view('city.edit', [
            'city' => $city,
            'departments' => ResearchStaffDepartment::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, ResearchStaffCity $city): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cities,name,' . $city->id,
            'department_id' => 'required|exists:departments,id',
        ]);

        $city->update($data);

        return redirect()
            ->route('cities.index')
            ->with('success', "Ciudad '{$city->name}' actualizada correctamente.");
    }

    public function destroy(ResearchStaffCity $city): RedirectResponse
    {
        try {
            $name = $city->name;
            $city->delete();

            return redirect()
                ->route('cities.index')
                ->with('success', "Ciudad '{$name}' eliminada correctamente.");
        } catch (QueryException $exception) {
            return redirect()
                ->route('cities.index')
                ->with('error', 'No se puede eliminar la ciudad porque tiene información relacionada.');
        }
    }

    public function byDepartment(ResearchStaffDepartment $department): JsonResponse
    {
        $cities = $department->cities()
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($cities);
    }
}