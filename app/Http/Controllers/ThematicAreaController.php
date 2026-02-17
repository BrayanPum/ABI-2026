<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffInvestigationLine;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use App\Models\ResearchStaff\ResearchStaffThematicArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThematicAreaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $researchGroupId = $request->get('research_group_id');
        $investigationLineId = $request->get('investigation_line_id');
        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $thematicAreas = ResearchStaffThematicArea::query()
            ->with(['investigationLine.researchGroup'])
            ->when($search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($investigationLineId, function ($query, $lineId) {
                $query->where('investigation_line_id', $lineId);
            })
            ->when($researchGroupId, function ($query, $groupId) {
                $query->whereHas('investigationLine', function ($q) use ($groupId) {
                    $q->where('research_group_id', $groupId);
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        $researchGroups = ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id');
        $investigationLines = ResearchStaffInvestigationLine::with('researchGroup')
            ->orderBy('name')
            ->get();

        return view('thematic-areas.index', [
            'thematicAreas' => $thematicAreas,
            'researchGroups' => $researchGroups,
            'investigationLines' => $investigationLines,
            'search' => $search,
            'researchGroupId' => $researchGroupId,
            'investigationLineId' => $investigationLineId,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('thematic-areas.create', [
            'thematicArea' => new ResearchStaffThematicArea(),
            'investigationLines' => ResearchStaffInvestigationLine::with('researchGroup')->orderBy('name')->get(),
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:thematic_areas,name',
            'description' => 'required|string|min:10',
            'investigation_line_id' => 'required|exists:investigation_lines,id',
        ]);

        $thematicArea = ResearchStaffThematicArea::create($data);

        return redirect()
            ->route('thematic-areas.index')
            ->with('success', "Área temática '{$thematicArea->name}' creada correctamente.");
    }

    public function show(ResearchStaffThematicArea $thematicArea): View
    {
        $thematicArea->load(['investigationLine.researchGroup']);

        return view('thematic-areas.show', compact('thematicArea'));
    }

    public function edit(ResearchStaffThematicArea $thematicArea): View
    {
        return view('thematic-areas.edit', [
            'thematicArea' => $thematicArea,
            'investigationLines' => ResearchStaffInvestigationLine::with('researchGroup')->orderBy('name')->get(),
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, ResearchStaffThematicArea $thematicArea): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:thematic_areas,name,' . $thematicArea->id,
            'description' => 'required|string|min:10',
            'investigation_line_id' => 'required|exists:investigation_lines,id',
        ]);

        $thematicArea->update($data);

        return redirect()
            ->route('thematic-areas.index')
            ->with('success', "Área temática '{$thematicArea->name}' actualizada correctamente.");
    }

    public function destroy(ResearchStaffThematicArea $thematicArea): RedirectResponse
    {
        $name = $thematicArea->name;
        $thematicArea->delete();

        return redirect()
            ->route('thematic-areas.index')
            ->with('success', "Área temática '{$name}' eliminada correctamente.");
    }
}
