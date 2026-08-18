<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\GuestType;
use App\Enums\RsvpStatus;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Lead;
use App\Models\Message;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'invited' => Guest::count(),
                'confirmed' => Guest::where('rsvp_status', RsvpStatus::Confirmed)->count(),
                'attended' => Guest::where('attendance_status', AttendanceStatus::Attended)->count(),
                'awaited' => Guest::where('attendance_status', AttendanceStatus::Awaited)->count(),
                'vip' => Guest::where('guest_type', GuestType::Vip)->count(),
                'leads' => Lead::count(),
                'messages_new' => Message::where('status', 'new')->count(),
            ],
            'latestCheckins' => Guest::whereNotNull('checked_in_at')
                ->latest('checked_in_at')->limit(10)->get(),
        ]);
    }
}
