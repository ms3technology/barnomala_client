<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TeacherSyncController;
use App\Http\Controllers\Api\TransferExportController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    Route::post('/sync', [SyncController::class, 'syncOptions']);
    Route::post('/teachers/sync', [TeacherSyncController::class, 'sync']);

    Route::prefix('export/barnomala/v1')->group(function () {
        Route::get('students', [TransferExportController::class, 'students']);
        Route::get('student/enrollments', [TransferExportController::class, 'studentEnrollments']);
        Route::get('subjects', [TransferExportController::class, 'subjects']);
        Route::get('users', [TransferExportController::class, 'users']);
        Route::get('teachers', [TransferExportController::class, 'teachers']);
        Route::get('exams', [TransferExportController::class, 'exams']);
        Route::get('exams/schedules', [TransferExportController::class, 'examSchedules']);
        Route::get('exams/results', [TransferExportController::class, 'examResults']);
        Route::get('slider-images', [TransferExportController::class, 'sliderImages']);
        Route::get('committees', [TransferExportController::class, 'committees']);
        Route::get('governing-body', [TransferExportController::class, 'governingBody']);
        Route::get('options', [TransferExportController::class, 'options']);
        Route::get('speeches', [TransferExportController::class, 'speeches']);
    });
});
