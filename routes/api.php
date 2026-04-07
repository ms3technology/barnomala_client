<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TeacherSyncController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    Route::post('/sync', [SyncController::class, 'syncOptions']);
    Route::post('/teachers/sync', [TeacherSyncController::class, 'sync']);
});
