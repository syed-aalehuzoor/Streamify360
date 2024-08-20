<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/videos', function () {
        return view('videos');
    })->name('all-videos');
    Route::get('/videos/upload-new', function () {
        return view('upload-video');
    })->name('add-video');
    Route::get('/settings/general', function () {
        return view('general-settings');
    })->name('general-settings');
    Route::get('/settings/video', function () {
        return view('videos-settings');
    })->name('video-settings');
    Route::get('/servers', function () {
        return view('servers');
    })->name('all-servers');
    Route::get('/servers/add-new', function () {
        return view('add-server');
    })->name('add-server');
});

Route::middleware([
    'admin',
])->group(function () {

    // Dashboard Routes
    Route::get('/admin', [DashboardController::class, 'dashboard'])->name('admin');
    Route::get('/admin/videos', [DashboardController::class, 'videos'])->name('admin-all-videos');
    Route::get('/admin/processes', [DashboardController::class, 'processes'])->name('Processes');
    Route::get('/admin/abuse-reports', [DashboardController::class, 'abuseReports'])->name('abuse-reports');

    // Server Manager Routes
    Route::get('/admin/servers', [ServerController::class, 'index'])->name('admin-servers');
    Route::get('/admin/servers/add', [ServerController::class, 'create'])->name('admin-add-server');
    Route::post('/admin/servers', [ServerController::class, 'store'])->name('admin-store-server');

    // User Manager Routes
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin-users');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/admin/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('/admin/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

});