<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApprovalStatus;
use App\Enums\GuestType;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $guests = Guest::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = trim($request->query('q'));
                $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('mobile', 'like', "%{$q}%"));
            })
            ->when($request->filled('type'), fn ($query) => $query->where('guest_type', $request->query('type')))
            ->when($request->filled('attendance'), fn ($query) => $query->where('attendance_status', $request->query('attendance')))
            ->when($request->filled('approval'), fn ($query) => $query->where('approval_status', $request->query('approval')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.guests.index', [
            'guests' => $guests,
            'types' => GuestType::cases(),
            'approvals' => ApprovalStatus::cases(),
            'pendingCount' => Guest::pending()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'mobile' => ['required', 'string', 'max:30', 'unique:guests,mobile'],
            'email' => ['nullable', 'email', 'max:190'],
            'guest_type' => ['required', Rule::in(array_column(GuestType::cases(), 'value'))],
        ]);

        // ما يضيفه الموظف بنفسه معتمد مباشرة — المراجعة للطلبات القادمة من الموقع
        $guest = Guest::create($data + [
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rsvp_status' => 'confirmed',
            'rsvp_at' => now(),
            'registered_via' => 'staff',
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.guests.index')
            ->with('success', "تمت إضافة الضيف «{$guest->name}» وتوليد بطاقته — أرسلها له من الجدول.");
    }

    /** اعتماد طلب حضور قادم من الموقع — عندها فقط تصبح بطاقة الدعوة صالحة للإرسال */
    public function approve(Request $request, Guest $guest): RedirectResponse
    {
        $guest->update([
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rejection_reason' => null,
            'rsvp_status' => 'confirmed',
            'rsvp_at' => now(),
        ]);

        return back()->with('success', "تم اعتماد «{$guest->name}» — أرسل له بطاقة الدعوة من زر واتساب أو البريد.");
    }

    public function reject(Request $request, Guest $guest): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $guest->update([
            'approval_status' => ApprovalStatus::Rejected,
            'approved_at' => null,
            'approved_by' => $request->user()->id,
            'rejection_reason' => $data['rejection_reason'] ?? null,
            'rsvp_status' => 'declined',
        ]);

        return back()->with('success', "تم رفض طلب «{$guest->name}».");
    }

    /** تسجيل أن البطاقة أُرسلت — يضغطه الموظف بعد فتح واتساب أو البريد */
    public function markSent(Request $request, Guest $guest): RedirectResponse
    {
        $via = $request->validate([
            'via' => ['required', Rule::in(['whatsapp', 'email'])],
        ])['via'];

        if (! $guest->isApproved()) {
            return back()->withErrors(['via' => 'لا يمكن إرسال بطاقة لطلب غير معتمد.']);
        }

        $guest->update(['card_sent_at' => now(), 'card_sent_via' => $via]);

        return back()->with('success', "سُجّل إرسال بطاقة «{$guest->name}» عبر ".($via === 'whatsapp' ? 'واتساب' : 'البريد').'.');
    }
}
