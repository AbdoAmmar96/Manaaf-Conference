<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $messages = Message::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->query('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.messages.index', ['messages' => $messages]);
    }
}
