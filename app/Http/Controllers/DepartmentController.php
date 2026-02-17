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
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $departments = ResearchStaffDepartment::query()
            ->withCount('cities')
            ->when($search, function ($query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        return view('departments.index', [
            'departments' => $departments,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('departments.create', [
            'department' => new ResearchStaffDepartment(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
        ]);

        $department = ResearchStaffDepartment::create($data);

        return redirect()
            ->route('departments.index')
            ->with('success', "Departamento '{$department->name}' creado correctamente.");
    }

    public function show(ResearchStaffDepartment $department): View
    {
        $department->load(['cities' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('departments.show', compact('department'));
    }

    public function edit(ResearchStaffDepartment $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, ResearchStaffDepartment $department): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
        ]);

        $department->update($data);

        return redirect()
            ->route('departments.index')
            ->with('success', "Departamento '{$department->name}' actualizado correctamente.");
    }

    public function destroy(ResearchStaffDepartment $department): RedirectResponse
    {
        try {
            $name = $department->name;
            $department->delete();

            return redirect()
                ->route('departments.index')
                ->with('success', "Departamento '{$name}' eliminado correctamente.");
        } catch (QueryException $exception) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'No se puede eliminar el departamento porque tiene información relacionada.');
        }
    }

    public function cities(ResearchStaffDepartment $department): JsonResponse
    {
        $cities = $department->cities()
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($cities);
    }
}