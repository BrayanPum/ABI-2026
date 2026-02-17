<?php

namespace App\Http\Controllers;

use App\Models\ContentFramework;
use App\Models\Framework;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentFrameworkController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $frameworkId = $request->get('framework_id');

        $query = ContentFramework::query()
            ->with('framework')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($frameworkId)) {
            $query->where('framework_id', $frameworkId);
        }

        $contentFrameworks = $query->paginate(15)->withQueryString();
        $frameworkOptions = Framework::orderBy('name')->pluck('name', 'id');

        return view('content-frameworks.index', compact('contentFrameworks', 'frameworkOptions'))
            ->with('search', $search)
            ->with('framework_id', $frameworkId);
    }

    public function create(Request $request): View
    {
        $contentFramework = new ContentFramework();
        $frameworks = Framework::orderBy('name')->pluck('name', 'id');
        $prefw = $request->get('framework_id');

        return view('content-frameworks.create', compact('contentFramework', 'frameworks', 'prefw'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('content_frameworks', 'name')->whereNull('deleted_at'),
            ],
            'description' => ['required', 'string'],
            'framework_id' => ['required', 'exists:frameworks,id'],
        ]);

        $contentFramework = ContentFramework::create($data);

        return redirect()
            ->route('content-frameworks.index')
            ->with('success', "Contenido '{$contentFramework->name}' creado correctamente.");
    }

    public function show(ContentFramework $contentFramework): View
    {
        $contentFramework->load('framework');

        return view('content-frameworks.show', compact('contentFramework'));
    }

    public function edit(ContentFramework $contentFramework): View
    {
        $frameworks = Framework::orderBy('name')->pluck('name', 'id');

        return view('content-frameworks.edit', compact('contentFramework', 'frameworks'));
    }

    public function update(Request $request, ContentFramework $contentFramework): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('content_frameworks', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($contentFramework->id),
            ],
            'description' => ['required', 'string'],
            'framework_id' => ['required', 'exists:frameworks,id'],
        ]);

        $contentFramework->update($data);

        return redirect()
            ->route('content-frameworks.index')
            ->with('success', "Contenido '{$contentFramework->name}' actualizado correctamente.");
    }

    public function destroy(ContentFramework $contentFramework): RedirectResponse
    {
        $contentFramework->delete();

        return redirect()
            ->route('content-frameworks.index')
            ->with('success', "Contenido '{$contentFramework->name}' eliminado correctamente.");
    }
}
