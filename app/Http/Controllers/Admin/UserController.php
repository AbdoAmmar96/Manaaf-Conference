<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get(),
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create($data + ['active' => true]);

        return back()->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'active' => ['nullable', 'boolean'],
        ]);

        // منع المدير من سحب صلاحيته أو تعطيل حسابه بنفسه فيُقفل خارج النظام
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role->value;
            $data['active'] = true;
        } else {
            $data['active'] = $request->boolean('active');
        }

        // آخر مدير نشط يجب أن يبقى مديرًا نشطًا
        if ($user->isAdmin() && ($data['role'] !== UserRole::Admin->value || ! $data['active'])) {
            $otherAdmins = User::where('role', UserRole::Admin)
                ->where('active', true)->where('id', '!=', $user->id)->count();

            if ($otherAdmins === 0) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors([
                        'role' => 'لا يمكن تغيير دور آخر مدير نشط أو تعطيله — عيّن مديرًا آخر أولًا.',
                    ]);
            }
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث بيانات المستخدم.');
    }
}
