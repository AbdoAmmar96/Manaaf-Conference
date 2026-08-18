<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        return view('admin.permissions', [
            'matrix' => Permissions::matrix(),
            'summaries' => Permissions::roleSummaries(),
            'roles' => UserRole::cases(),
            'counts' => User::where('active', true)
                ->selectRaw('role, count(*) as total')->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }
}
