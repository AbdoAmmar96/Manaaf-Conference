<?php

namespace App\Http\Controllers\Admin;

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
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.guests.index', [
            'guests' => $guests,
            'types' => GuestType::cases(),
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

        $guest = Guest::create($data + [
            'rsvp_status' => 'confirmed',
            'rsvp_at' => now(),
            'registered_via' => 'staff',
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.guests.index')
            ->with('success', "تمت إضافة الضيف «{$guest->name}» وتوليد بطاقته — أرسلها له من زر واتساب في الجدول.");
    }
}
