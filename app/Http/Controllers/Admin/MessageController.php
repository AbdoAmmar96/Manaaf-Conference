<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MessageStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageComment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $messages = Message::query()
            ->with(['zone', 'assignee', 'comments.user'])
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->query('category')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('zone'), fn ($q) => $q->where('zone_id', $request->query('zone')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.messages.index', [
            'messages' => $messages,
            'statuses' => MessageStatus::cases(),
            'zones' => Zone::orderBy('name')->get(),
            'team' => User::where('active', true)->orderBy('name')->get(),
            'stats' => [
                'total' => Message::count(),
                'new' => Message::where('status', MessageStatus::New)->count(),
                'in_progress' => Message::where('status', MessageStatus::InProgress)->count(),
                'closed' => Message::where('status', MessageStatus::Closed)->count(),
            ],
        ]);
    }

    /** متابعة الحالة — تغيير الحالة و/أو تعيين موظف مسؤول */
    public function update(Request $request, Message $message): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(MessageStatus::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $message->update($data);

        return back()->with('success', 'تم تحديث متابعة الرسالة.');
    }

    /** إضافة ملاحظة متابعة على الرسالة */
    public function comment(Request $request, Message $message): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'status' => ['nullable', Rule::enum(MessageStatus::class)],
        ]);

        MessageComment::create([
            'message_id' => $message->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        // الملاحظة قد تُرفق بتغيير الحالة في نفس الخطوة
        if (! empty($data['status'])) {
            $message->update(['status' => $data['status']]);
        }

        return back()->with('success', 'تمت إضافة الملاحظة إلى سجل المتابعة.');
    }
}
