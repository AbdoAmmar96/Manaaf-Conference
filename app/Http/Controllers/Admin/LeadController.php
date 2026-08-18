<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadScore;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Interest;
use App\Models\Lead;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->with(['guest', 'zone', 'assignee'])
            ->when($request->filled('score'), fn ($q) => $q->where('score', $request->query('score')))
            ->when($request->filled('zone'), fn ($q) => $q->where('zone_id', $request->query('zone')))
            ->when($request->filled('interest'), fn ($q) => $q->whereJsonContains('interests', $request->query('interest')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'interests' => Interest::active()->orderBy('sort')->get(),
            'scores' => LeadScore::cases(),
            'zones' => Zone::orderBy('name')->get(),
            'salesTeam' => User::whereIn('role', [UserRole::Sales, UserRole::Admin])
                ->where('active', true)->orderBy('name')->get(),
            'guests' => Guest::orderBy('name')->get(['id', 'name', 'mobile']),
            'stats' => [
                'total' => Lead::count(),
                'hot' => Lead::where('score', LeadScore::Hot)->count(),
                'warm' => Lead::where('score', LeadScore::Warm)->count(),
                'cold' => Lead::where('score', LeadScore::Cold)->count(),
            ],
        ]);
    }

    /** تسجيل اهتمام جديد — موظف المبيعات يفتح ملف الضيف ويحدد اهتمامه */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guest_id' => ['nullable', 'exists:guests,id'],
            'walk_in_name' => ['required_without:guest_id', 'nullable', 'string', 'max:190'],
            'walk_in_mobile' => ['required_without:guest_id', 'nullable', 'string', 'max:30'],
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => [Rule::exists('interests', 'slug')],
            'score' => ['required', Rule::enum(LeadScore::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['nullable', 'date'],
            'zone_id' => ['nullable', 'exists:zones,id'],
        ]);

        // لو اختير ضيف مسجل فلا داعي لبيانات الزائر اليدوية
        if (! empty($data['guest_id'])) {
            $data['walk_in_name'] = null;
            $data['walk_in_mobile'] = null;
        }

        Lead::create($data + ['source' => 'sales', 'created_by' => $request->user()->id]);

        return back()->with('success', 'تم تسجيل الاهتمام بنجاح.');
    }

    /** تعديل الدرجة والاهتمامات والملاحظات والمتابعة */
    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => [Rule::exists('interests', 'slug')],
            'score' => ['required', Rule::enum(LeadScore::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $lead->update($data);

        return back()->with('success', 'تم تحديث بيانات الاهتمام.');
    }
}
