<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\GuestType;
use App\Enums\LeadScore;
use App\Enums\MessageStatus;
use App\Enums\RsvpStatus;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Interest;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Zone;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'guests' => [
                'total' => Guest::count(),
                'confirmed' => Guest::where('rsvp_status', RsvpStatus::Confirmed)->count(),
                'declined' => Guest::where('rsvp_status', RsvpStatus::Declined)->count(),
                'pending' => Guest::where('rsvp_status', RsvpStatus::Pending)->count(),
                'attended' => Guest::where('attendance_status', AttendanceStatus::Attended)->count(),
                'awaited' => Guest::where('attendance_status', AttendanceStatus::Awaited)->count(),
                'cancelled' => Guest::where('attendance_status', AttendanceStatus::Cancelled)->count(),
            ],
            'byType' => collect(GuestType::cases())->map(fn ($t) => [
                'label' => $t->labelAr(),
                'count' => Guest::where('guest_type', $t)->count(),
            ]),
            'byInterest' => Interest::orderBy('sort')->get()->map(fn ($i) => [
                'label' => $i->name,
                'count' => Lead::whereJsonContains('interests', $i->slug)->count(),
            ])->sortByDesc('count')->values(),
            'byScore' => collect(LeadScore::cases())->map(fn ($s) => [
                'label' => $s->labelAr(),
                'count' => Lead::where('score', $s)->count(),
            ]),
            'byZone' => Zone::withCount('leads')->orderByDesc('leads_count')->get(),
            'messages' => collect(MessageStatus::cases())->map(fn ($s) => [
                'label' => $s->labelAr(),
                'count' => Message::where('status', $s)->count(),
            ]),
            'leadsTotal' => Lead::count(),
            'messagesTotal' => Message::count(),
        ]);
    }

    /** تصدير الضيوف — CSV بترميز UTF-8 يفتح مباشرة في Excel */
    public function exportGuests(): StreamedResponse
    {
        $rows = Guest::with('checkedInBy')->orderBy('name')->cursor();

        return $this->stream('guests-'.now()->format('Y-m-d-Hi').'.csv', [
            'الاسم', 'الجهة', 'المنصب', 'الجوال', 'البريد', 'نوع الضيف',
            'تأكيد الحضور', 'حالة الحضور', 'وقت الدخول', 'سجّله',
        ], $rows, fn (Guest $g) => [
            $g->name, $g->organization, $g->position, $g->mobile, $g->email,
            $g->guest_type->labelAr(), $g->rsvp_status->labelAr(), $g->attendance_status->labelAr(),
            $g->checked_in_at?->format('Y-m-d H:i'), $g->checkedInBy?->name,
        ]);
    }

    /** تصدير اهتمامات العملاء بكل تفاصيلها */
    public function exportLeads(): StreamedResponse
    {
        $rows = Lead::with(['guest', 'zone', 'assignee'])->latest()->cursor();

        return $this->stream('leads-'.now()->format('Y-m-d-Hi').'.csv', [
            'التاريخ', 'العميل', 'الجوال', 'الجهة', 'الاهتمامات', 'الدرجة',
            'المصدر', 'المنطقة', 'الموظف المسؤول', 'موعد المتابعة', 'ملاحظات',
        ], $rows, fn (Lead $l) => [
            $l->created_at->format('Y-m-d H:i'),
            $l->displayName(),
            $l->guest?->mobile ?? $l->walk_in_mobile,
            $l->guest?->organization,
            Interest::labelsFor($l->interests)->implode('، '),
            $l->score->labelAr(),
            $l->source->labelAr(),
            $l->zone?->name,
            $l->assignee?->name,
            $l->follow_up_at?->format('Y-m-d'),
            $l->notes,
        ]);
    }

    /** تصدير الرسائل مع سجل المتابعة */
    public function exportMessages(): StreamedResponse
    {
        $rows = Message::with(['zone', 'assignee', 'comments.user'])->latest()->cursor();

        return $this->stream('messages-'.now()->format('Y-m-d-Hi').'.csv', [
            'التاريخ', 'المرسل', 'الجوال', 'البريد', 'الموضوع', 'الرسالة',
            'التصنيف', 'الحالة', 'المنطقة', 'الموظف المسؤول', 'سجل المتابعة',
        ], $rows, fn (Message $m) => [
            $m->created_at->format('Y-m-d H:i'),
            $m->name, $m->mobile, $m->email, $m->subject, $m->body,
            $m->category->labelAr(), $m->status->labelAr(),
            $m->zone?->name, $m->assignee?->name,
            $m->comments->map(fn ($c) => $c->user->name.': '.$c->body)->implode(' | '),
        ]);
    }

    /**
     * يكتب CSV على دفعات بدل تحميله كاملًا في الذاكرة.
     * BOM في المقدمة ضروري وإلا فتحت Excel العربية كرموز مشوّشة.
     */
    private function stream(string $filename, array $headers, iterable $rows, callable $map): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows, $map) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, $map($row));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
