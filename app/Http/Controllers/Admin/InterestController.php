<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Lead;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InterestController extends Controller
{
    public function index(): View
    {
        $interests = Interest::with('projects')->orderBy('sort')->orderBy('name')->get();

        // عدد الاهتمامات المسجلة لكل مجال — leads.interests يخزّن الـslug
        $counts = $interests->mapWithKeys(fn ($i) => [
            $i->id => Lead::whereJsonContains('interests', $i->slug)->count(),
        ]);

        return view('admin.interests.index', [
            'interests' => $interests,
            'counts' => $counts,
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'projects' => ['array'],
            'projects.*' => ['exists:projects,id'],
        ]);

        $interest = Interest::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'active' => true,
        ]);
        $interest->projects()->sync($request->input('projects', []));

        return back()->with('success', "تمت إضافة مجال «{$interest->name}».");
    }

    public function update(Request $request, Interest $interest): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
            'active' => ['nullable', 'boolean'],
            'projects' => ['array'],
            'projects.*' => ['exists:projects,id'],
        ]);

        $interest->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort' => $data['sort'] ?? 0,
            'active' => $request->boolean('active'),
        ]);
        $interest->projects()->sync($request->input('projects', []));

        return back()->with('success', "تم تحديث مجال «{$interest->name}».");
    }

    public function destroy(Interest $interest): RedirectResponse
    {
        /*
         * الاهتمامات المسجلة تخزّن الـslug نصًا، فحذف المجال لا يكسر البيانات
         * لكنه يفقدها اسمها المعروض. نمنع الحذف ونقترح التعطيل بدلًا منه.
         */
        $used = Lead::whereJsonContains('interests', $interest->slug)->count();

        if ($used > 0) {
            return back()->withErrors([
                'name' => "لا يمكن حذف «{$interest->name}» لوجود {$used} اهتمام مسجّل عليه — عطّله بدل حذفه ليختفي من النماذج مع بقاء التقارير سليمة.",
            ]);
        }

        $name = $interest->name;
        $interest->delete();

        return back()->with('success', "تم حذف مجال «{$name}».");
    }
}
