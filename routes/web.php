<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


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
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin');
    Route::get('/admin/videos', function () {
        return view('admin.videos');
    })->name('admin-all-videos');
    Route::get('/admin/processes', function () {
        return view('admin.processes');
    })->name('Processes');
    Route::get('/admin/abuse-reports', function () {
        return view('admin.abuse-reports');
    })->name('abuse-reports');
    Route::get('/admin/servers', function () {
        return view('admin.servers');
    })->name('admin-servers');
    Route::get('/admin/servers/add', function () {
        return view('admin.add-servers');
    })->name('admin-add-server');
});