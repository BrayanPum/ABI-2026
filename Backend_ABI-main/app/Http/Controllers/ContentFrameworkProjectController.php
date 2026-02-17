<?php

namespace App\Http\Controllers;

use App\Models\ContentFramework;
use App\Models\ContentFrameworkProject;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentFrameworkProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $projectId = $request->get('project_id');
        $contentFrameworkId = $request->get('content_framework_id');

        $query = ContentFrameworkProject::query()
            ->with(['project', 'contentFramework'])
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->whereHas('project', function ($relation) use ($search) {
                        $relation->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contentFramework', function ($relation) use ($search) {
                        $relation->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($projectId)) {
            $query->where('project_id', $projectId);
        }

        if (!empty($contentFrameworkId)) {
            $query->where('content_framework_id', $contentFrameworkId);
        }

        $contentFrameworkProjects = $query->paginate(15)->withQueryString();

        $projectOptions = Project::orderBy('title')->pluck('title', 'id');
        $contentFrameworkOptions = ContentFramework::orderBy('name')->pluck('name', 'id');

        return view('content-framework-project.index', compact('contentFrameworkProjects', 'projectOptions', 'contentFrameworkOptions'))
            ->with('search', $search)
            ->with('projectId', $projectId)
            ->with('contentFrameworkId', $contentFrameworkId);
    }

    public function create(Request $request): View
    {
        $contentFrameworkProject = new ContentFrameworkProject();
        $projects = Project::orderBy('title')->pluck('title', 'id');
        $contentFrameworks = ContentFramework::orderBy('name')->pluck('name', 'id');
        $projectId = $request->get('project_id');
        $contentFrameworkId = $request->get('content_framework_id');

        return view('content-framework-project.create', compact(
            'contentFrameworkProject',
            'projects',
            'contentFrameworks',
            'projectId',
            'contentFrameworkId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'content_framework_id' => [
                'required',
                'exists:content_frameworks,id',
                Rule::unique('content_framework_project', 'content_framework_id')
                    ->where(fn ($query) => $query->where('project_id', $request->input('project_id'))),
            ],
        ]);

        $contentFrameworkProject = ContentFrameworkProject::create($data);

        return redirect()
            ->route('content-framework-project.index')
            ->with('success', 'Asignación creada correctamente.');
    }

    public function show(ContentFrameworkProject $contentFrameworkProject): View
    {
        $contentFrameworkProject->load(['project', 'contentFramework']);

        return view('content-framework-project.show', compact('contentFrameworkProject'));
    }

    public function edit(ContentFrameworkProject $contentFrameworkProject): View
    {
        $projects = Project::orderBy('title')->pluck('title', 'id');
        $contentFrameworks = ContentFramework::orderBy('name')->pluck('name', 'id');

        return view('content-framework-project.edit', compact('contentFrameworkProject', 'projects', 'contentFrameworks'));
    }

    public function update(Request $request, ContentFrameworkProject $contentFrameworkProject): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'content_framework_id' => [
                'required',
                'exists:content_frameworks,id',
                Rule::unique('content_framework_project', 'content_framework_id')
                    ->where(fn ($query) => $query->where('project_id', $request->input('project_id')))
                    ->ignore($contentFrameworkProject->id),
            ],
        ]);

        $contentFrameworkProject->update($data);

        return redirect()
            ->route('content-framework-project.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(ContentFrameworkProject $contentFrameworkProject): RedirectResponse
    {
        $contentFrameworkProject->delete();

        return redirect()
            ->route('content-framework-project.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }
}
