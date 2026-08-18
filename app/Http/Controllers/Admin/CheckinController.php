<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApprovalStatus;
use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function index(): View
    {
        return view('admin.checkin');
    }

    public function lookup(Request $request): JsonResponse
    {
        if ($token = $request->query('token')) {
            $guest = Guest::where('qr_token', $token)->first();

            return response()->json(['guests' => $guest ? [$this->card($guest)] : []]);
        }

        $q = trim((string) $request->query('q'));

        if (mb_strlen($q) < 2) {
            return response()->json(['guests' => []]);
        }

        $guests = Guest::where('name', 'like', "%{$q}%")
            ->orWhere('mobile', 'like', "%{$q}%")
            ->orderBy('name')->limit(8)->get();

        return response()->json(['guests' => $guests->map(fn ($g) => $this->card($g))]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $guest = Guest::findOrFail($request->integer('guest_id'));

        if (! $guest->isApproved()) {
            return response()->json(['status' => 'not_approved', 'guest' => $this->card($guest)]);
        }

        if ($guest->attendance_status === AttendanceStatus::Cancelled) {
            return response()->json(['status' => 'cancelled', 'guest' => $this->card($guest)]);
        }

        if ($guest->isCheckedIn()) {
            return response()->json(['status' => 'already', 'guest' => $this->card($guest)]);
        }

        $guest->update([
            'attendance_status' => AttendanceStatus::Attended,
            'checked_in_at' => now(),
            'checked_in_by' => $request->user()->id,
        ]);

        return response()->json(['status' => 'ok', 'guest' => $this->card($guest->fresh())]);
    }

    /* صفحة تُفتح عند مسح QR الضيف بكاميرا الجوال مباشرة */
    public function scan(string $token): View
    {
        $guest = Guest::where('qr_token', $token)->first();

        return view('admin.scan', ['guest' => $guest]);
    }

    public function scanConfirm(Request $request, string $token): RedirectResponse
    {
        $guest = Guest::where('qr_token', $token)->firstOrFail();

        if ($guest->isApproved() && $guest->attendance_status !== AttendanceStatus::Cancelled && ! $guest->isCheckedIn()) {
            $guest->update([
                'attendance_status' => AttendanceStatus::Attended,
                'checked_in_at' => now(),
                'checked_in_by' => $request->user()->id,
            ]);

            return redirect()->route('checkin.scan', ['token' => $token])->with('just_checked_in', true);
        }

        return redirect()->route('checkin.scan', ['token' => $token]);
    }

    private function card(Guest $g): array
    {
        return [
            'id' => $g->id,
            'name' => $g->name,
            'organization' => $g->organization,
            'position' => $g->position,
            'mobile' => $g->mobile,
            'type' => $g->guest_type->value,
            'type_label' => $g->guest_type->labelAr(),
            'rsvp_label' => $g->rsvp_status->labelAr(),
            'approval' => $g->approval_status->value,
            'approval_label' => $g->approval_status->labelAr(),
            'attendance' => $g->attendance_status->value,
            'attendance_label' => $g->attendance_status->labelAr(),
            'checked_in_at' => $g->checked_in_at?->format('h:i A'),
            'checked_in_by' => $g->checkedInBy?->name,
        ];
    }
}
