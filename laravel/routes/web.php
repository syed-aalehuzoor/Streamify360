<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AnalyticsController;
use App\Helpers\EncoderApiAuth;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\PlayerSettingsController;
use App\Http\Controllers\SubscriptionPlanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(){
    $apitoken = EncoderApiAuth::generateApiToken();
    dd($apitoken);
});

Route::get('/home', [HomeController::class, 'index']);
Route::get('/video/{id}', [VideoController::class, 'player'])->name('video.player');

Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'google_callback']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // User Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Video Routes
    Route::get('/videos', [VideoController::class, 'index'])->name('all-videos');
    Route::get('/videos/drafts', [VideoController::class, 'drafts'])->name('drafts-videos');
    Route::get('/videos/upload-new', [VideoController::class, 'create'])->name('add-video');
    Route::post('videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/videos/{id}/edit', [VideoController::class, 'edit'])->name('videos.edit');
    Route::post('/videos/{id}/edit', [VideoController::class, 'save'])->name('videos.edit');
    Route::put('/videos/{id}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])->name('videos.destroy');

    //Users Analytics Routes
    Route::get('/Analytics/video-performance', [AnalyticsController::class, 'video'])->name('video-performance');
    Route::get('/Analytics/audiance', [AnalyticsController::class, 'audiance'])->name('audience-insights');

    //Users Settings Routes
    Route::get('/settings/player', [PlayerSettingsController::class, 'edit'])->name('player-settings.edit');
    Route::post('/settings/player', [PlayerSettingsController::class, 'update'])->name('player-settings.update');
    
    //Users Subscription Routes
    Route::get('/plan/subscription', [SubscriptionPlanController::class, 'index'])->name('subscription');

    Route::middleware([
        'userplan:premium',
    ])->group(function () {
        //Users Subscription Routes
        Route::get('/settings/ads', [SettingsController::class, 'general'])->name('ad-settings');
        Route::get('/tools/sub', [SettingsController::class, 'general'])->name('subtitle-translator');
        Route::get('/tools', [SettingsController::class, 'general'])->name('tools');    
    });

    Route::middleware([
        'admin',
    ])->group(function () {
    
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('settings.update');
    
    
        // Dashboard Routes
        Route::get('/admin', [AdminDashboardController::class, 'dashboard'])->name('admin');
        Route::get('/admin/videos', [AdminDashboardController::class, 'videos'])->name('admin-all-videos');
        Route::get('/admin/processes', [AdminDashboardController::class, 'processes'])->name('Processes');
        Route::get('/admin/abuse-reports', [AdminDashboardController::class, 'abuseReports'])->name('abuse-reports');
    
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

        Route::post('/admin/video-settings', [ServerController::class, 'store'])->name('video-settings');
    
    });
});

