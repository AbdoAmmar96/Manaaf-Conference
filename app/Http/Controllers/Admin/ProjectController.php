<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Project;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with(['interests', 'zone'])
            ->when($request->filled('interest'), fn ($q) => $q->whereHas(
                'interests', fn ($i) => $i->where('interests.id', $request->query('interest'))
            ))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderBy('sort')->orderBy('name')
            ->get();

        return view('admin.projects.index', [
            'projects' => $projects,
            'interests' => Interest::orderBy('sort')->get(),
            'zones' => Zone::orderBy('name')->get(),
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $project = Project::create($data);
        $project->interests()->sync($request->input('interests', []));

        return back()->with('success', "تمت إضافة مشروع «{$project->name}».");
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request, $project);

        $project->update($data + ['active' => $request->boolean('active')]);
        $project->interests()->sync($request->input('interests', []));

        return back()->with('success', "تم تحديث مشروع «{$project->name}».");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $name = $project->name;
        $project->delete();   // الربط بمجالات الاهتمام يُحذف تلقائيًا (cascade)

        return back()->with('success', "تم حذف مشروع «{$name}».");
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:190'],
            'area' => ['nullable', 'string', 'max:60'],
            'units' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'interests' => ['array'],
            'interests.*' => ['exists:interests,id'],
        ]);

        unset($data['interests']);
        $data['sort'] = $data['sort'] ?? 0;

        return $data;
    }
}
