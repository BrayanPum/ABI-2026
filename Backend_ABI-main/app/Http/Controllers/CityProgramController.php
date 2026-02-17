<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Program;
use App\Models\CityProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CityProgramController extends Controller
{
    /**
     * Display a listing of the city-program assignments.
     */
    public function index(Request $request): View
    {
        // Determine the pagination size while keeping sensible boundaries.
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        // Retrieve assignments with the related city and program ready for rendering.
        $cityPrograms = CityProgram::with(['city', 'program'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        return view('city-program.index', [
            'cityPrograms' => $cityPrograms,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(): View
    {
        // Collect dropdown data to allow selecting the related city and program.
        return view('city-program.create', [
            'cityProgram' => new CityProgram(),
            'cities' => City::orderBy('name')->pluck('name', 'id'),
            'programs' => Program::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming payload enforcing referential integrity and uniqueness.
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'program_id' => [
                'required',
                'exists:programs,id',
                Rule::unique('city_program', 'program_id')->where(function ($query) use ($request) {
                    return $query->where('city_id', $request->input('city_id'));
                }),
            ],
        ]);

        try {
            // Persist the assignment once validation passes.
            CityProgram::create($validated);

            return redirect()
                ->route('city-program.index')
                ->with('success', 'La relación entre ciudad y programa se creó correctamente.');
        } catch (Throwable $exception) {
            // On failure, return to the form keeping the submitted data and showing feedback.
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al crear la relación. Intenta nuevamente.');
        }
    }

    /**
     * Display the specified assignment.
     */
    public function show(CityProgram $cityProgram): View
    {
        // Ensure the related entities are available for the detail view.
        $cityProgram->load(['city', 'program']);

        return view('city-program.show', compact('cityProgram'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(CityProgram $cityProgram): View
    {
        // Provide the current record alongside dropdown data to edit the relationship.
        return view('city-program.edit', [
            'cityProgram' => $cityProgram,
            'cities' => City::orderBy('name')->pluck('name', 'id'),
            'programs' => Program::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, CityProgram $cityProgram): RedirectResponse
    {
        // Validate the payload while ignoring the current assignment in the uniqueness check.
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'program_id' => [
                'required',
                'exists:programs,id',
                Rule::unique('city_program', 'program_id')
                    ->ignore($cityProgram->id)
                    ->where(function ($query) use ($request) {
                        return $query->where('city_id', $request->input('city_id'));
                    }),
            ],
        ]);

        try {
            // Apply the validated changes to the existing record.
            $cityProgram->update($validated);

            return redirect()
                ->route('city-program.index')
                ->with('success', 'La relación entre ciudad y programa se actualizó correctamente.');
        } catch (Throwable $exception) {
            // Report the exception and redirect back with contextual feedback.
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No fue posible actualizar la relación. Intenta nuevamente.');
        }
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(CityProgram $cityProgram): RedirectResponse
    {
        try {
            // Delete the relationship and redirect with a success notification.
            $cityProgram->delete();

            return redirect()
                ->route('city-program.index')
                ->with('success', 'La relación entre ciudad y programa se eliminó correctamente.');
        } catch (Throwable $exception) {
            // Provide error feedback if the deletion fails for any reason.
            report($exception);

            return redirect()
                ->route('city-program.index')
                ->with('error', 'No se pudo eliminar la relación solicitada.');
        }
    }
}
