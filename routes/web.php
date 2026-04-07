<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\Admin\NoticeController as AdminNoticeController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SpeechController as AdminSpeechController;
use App\Http\Controllers\Admin\OptionController as AdminOptionController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/up', function () {
    Artisan::call('migrate');
    Artisan::call('optimize:clear');
    return 'Database migrated and cache cleared!';
});

Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('notices', AdminNoticeController::class);
    Route::resource('news', AdminNewsController::class);
    Route::resource('speeches', AdminSpeechController::class);
    Route::post('speeches/quick', [AdminSpeechController::class, 'storeQuick'])->name('speeches.quick');
    Route::post('speeches/row-config', [AdminSpeechController::class, 'updateRowConfig'])->name('speeches.row-config');

    Route::get('branding', [AdminOptionController::class, 'branding'])->name('branding.index');
    Route::post('branding', [AdminOptionController::class, 'updateBranding'])->name('branding.update');

    Route::get('sliders', [AdminOptionController::class, 'slider'])->name('sliders.index');
    Route::post('sliders', [AdminOptionController::class, 'updateSlider'])->name('sliders.update');

    Route::get('settings', [AdminOptionController::class, 'settings'])->name('settings.index');
    Route::post('settings', [AdminOptionController::class, 'updateSettings'])->name('settings.update');

    Route::get('stats', [AdminOptionController::class, 'stats'])->name('stats.index');
    Route::post('stats', [AdminOptionController::class, 'updateStats'])->name('stats.update');

    Route::get('layout', [AdminOptionController::class, 'layout'])->name('layout.index');
    Route::post('layout', [AdminOptionController::class, 'updateLayout'])->name('layout.update');
});

Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/speeches', [PageController::class, 'speeches'])->name('speeches.index');
Route::get('/history', [PageController::class, 'history'])->name('history.index');
Route::get('/achievements', [PageController::class, 'achievements'])->name('achievements.index');
Route::get('/academic', [PageController::class, 'academic'])->name('academic.index');
Route::get('/results', [PageController::class, 'results'])->name('results.index');
Route::get('/teachers', [PageController::class, 'teachers'])->name('teachers.index');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact.index');
Route::get('/apply', [PageController::class, 'apply'])->name('apply.index');
Route::post('/contact-us', [PageController::class, 'contactSubmit'])->name('contact.submit');

Route::prefix('notices')->name('notices.')->group(function () {
    Route::get('/', [NoticeController::class, 'index'])->name('index');
    Route::get('/{notice}', [NoticeController::class, 'show'])->name('show');
});

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{news}', [NewsController::class, 'show'])->name('show');
});
