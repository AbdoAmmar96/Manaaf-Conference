<?php

use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| الواجهة العامة — بثيم manafi.sa
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/register', [PublicController::class, 'register'])->name('register');
Route::post('/register', [PublicController::class, 'registerStore'])->name('register.store');

Route::get('/rsvp/{token}', [PublicController::class, 'rsvp'])->name('rsvp');
Route::post('/rsvp/{token}', [PublicController::class, 'rsvpRespond'])->name('rsvp.respond');

Route::get('/my-qr/{token}', [PublicController::class, 'myQr'])->name('guest.qr');
Route::get('/qr-image/investor.png', [PublicController::class, 'investorQrImage'])->name('investor.qr.image');
Route::get('/qr-image/{token}.png', [PublicController::class, 'qrImage'])->name('guest.qr.image');

Route::get('/investors', [PublicController::class, 'investors'])->name('investors');
Route::get('/investor-request', [PublicController::class, 'investorRequest'])->name('investor.request');
Route::post('/investor-request', [PublicController::class, 'investorRequestStore'])->name('investor.request.store');

Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'contactStore'])->name('contact.store');

Route::get('/zones', [PublicController::class, 'zones'])->name('zones');
Route::get('/zones/{slug}/qr.png', [PublicController::class, 'zoneQrImage'])->name('zone.qr');

Route::get('/zone/{slug}', [PublicController::class, 'zone'])->name('zone');
Route::post('/zone/{slug}', [PublicController::class, 'zoneStore'])->name('zone.store');

/*
|--------------------------------------------------------------------------
| تسجيل الدخول للموظفين
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| مسح QR الضيف — الرابط المشفر داخل كود كل ضيف (للموظفين فقط)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,reception'])->group(function () {
    Route::get('/scan/{token}', [CheckinController::class, 'scan'])->name('checkin.scan');
    Route::post('/scan/{token}', [CheckinController::class, 'scanConfirm'])->name('checkin.scan.confirm');
});

/*
|--------------------------------------------------------------------------
| لوحة التحكم — Admin / Reception / Sales
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('role:admin,reception,sales')->name('dashboard');

    Route::middleware('role:admin,reception')->group(function () {
        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
        Route::patch('/guests/{guest}/approve', [GuestController::class, 'approve'])->name('guests.approve');
        Route::patch('/guests/{guest}/reject', [GuestController::class, 'reject'])->name('guests.reject');
        Route::patch('/guests/{guest}/sent', [GuestController::class, 'markSent'])->name('guests.sent');

        Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin');
        Route::get('/checkin/lookup', [CheckinController::class, 'lookup'])->name('checkin.lookup');
        Route::post('/checkin/confirm', [CheckinController::class, 'confirm'])->name('checkin.confirm');
    });

    /* الرسائل ومتابعتها — الثلاثة أدوار */
    Route::middleware('role:admin,reception,sales')->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::patch('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
        Route::post('/messages/{message}/comments', [MessageController::class, 'comment'])->name('messages.comment');

        /* اهتمامات العملاء — الاستقبال أيضًا يسجّل ويكتب الملاحظات */
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    });

    /* الإعدادات والتقارير — المدير فقط */
    Route::middleware('role:admin')->group(function () {
        /* حذف الضيف إجراء لا رجعة فيه، فهو للمدير وحده دون الاستقبال */
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

        Route::get('/zones', [ZoneController::class, 'index'])->name('zones.index');
        Route::post('/zones', [ZoneController::class, 'store'])->name('zones.store');
        Route::patch('/zones/{zone}', [ZoneController::class, 'update'])->name('zones.update');
        Route::get('/zones/{zone}/qr.png', [ZoneController::class, 'qrImage'])->name('zones.qr');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/guests.csv', [ReportController::class, 'exportGuests'])->name('reports.guests');
        Route::get('/reports/leads.csv', [ReportController::class, 'exportLeads'])->name('reports.leads');
        Route::get('/reports/messages.csv', [ReportController::class, 'exportMessages'])->name('reports.messages');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('/interests', [InterestController::class, 'index'])->name('interests.index');
        Route::post('/interests', [InterestController::class, 'store'])->name('interests.store');
        Route::patch('/interests/{interest}', [InterestController::class, 'update'])->name('interests.update');
        Route::delete('/interests/{interest}', [InterestController::class, 'destroy'])->name('interests.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions');
    });
});
