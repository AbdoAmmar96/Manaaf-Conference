<?php

use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MessageController;
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

        Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin');
        Route::get('/checkin/lookup', [CheckinController::class, 'lookup'])->name('checkin.lookup');
        Route::post('/checkin/confirm', [CheckinController::class, 'confirm'])->name('checkin.confirm');
    });

    Route::get('/messages', [MessageController::class, 'index'])
        ->middleware('role:admin,reception,sales')->name('messages.index');

    Route::get('/leads', [LeadController::class, 'index'])
        ->middleware('role:admin,sales')->name('leads.index');

    Route::view('/zones', 'admin.placeholder', ['title' => 'المناطق والماكيتات'])
        ->middleware('role:admin')->name('zones.index');

    Route::view('/reports', 'admin.placeholder', ['title' => 'التقارير'])
        ->middleware('role:admin')->name('reports');

    Route::view('/users', 'admin.placeholder', ['title' => 'المستخدمون والصلاحيات'])
        ->middleware('role:admin')->name('users.index');
});
