<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->with(['guest', 'zone', 'assignee'])
            ->when($request->filled('score'), fn ($q) => $q->where('score', $request->query('score')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.leads.index', ['leads' => $leads]);
    }
}
