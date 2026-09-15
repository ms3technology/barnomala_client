<?php

use App\Http\Controllers\Api\CommitteeSyncController;
use App\Http\Controllers\Api\DataTransferController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\GallerySyncController;
use App\Http\Controllers\Api\OptionSyncController;
use App\Http\Controllers\Api\PostCrudController;
use App\Http\Controllers\Api\StaffSyncController;
use App\Http\Controllers\Api\TeacherSyncController;
use App\Http\Controllers\Api\TransferExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('api.token')->group(function () {
        Route::post('upload', [FileUploadController::class, 'upload']);
        Route::post('options/sync', [OptionSyncController::class, 'sync']);
        Route::post('teachers/sync', [TeacherSyncController::class, 'sync']);
        Route::post('staff/sync', [StaffSyncController::class, 'sync']);
        Route::post('committees/sync', [CommitteeSyncController::class, 'sync']);
        Route::post('galleries/sync', [GallerySyncController::class, 'sync']);

        Route::get('posts', [PostCrudController::class, 'index']);
        Route::get('posts/{type}', [PostCrudController::class, 'indexByType'])
            ->where('type', '[a-z_]+');
        Route::get('posts/{id}', [PostCrudController::class, 'show'])
            ->where('id', '[0-9]+');

        // write operation endpoints
        Route::post('posts', [PostCrudController::class, 'store']);
        Route::match(['put', 'patch'], 'posts/{id}', [PostCrudController::class, 'update'])
            ->where('id', '[0-9]+');
        Route::delete('posts/{id}', [PostCrudController::class, 'destroy'])
            ->where('id', '[0-9]+');
    });

    Route::get('students', [TransferExportController::class, 'students']);
    Route::get('student/enrollments', [TransferExportController::class, 'studentEnrollments']);
    Route::get('subjects', [TransferExportController::class, 'subjects']);
    Route::get('classes', [TransferExportController::class, 'classes']);
    Route::get('teachers', [TransferExportController::class, 'teachers']);
    Route::get('exams', [TransferExportController::class, 'exams']);
    Route::get('exams/schedules', [TransferExportController::class, 'examSchedules']);
    Route::get('exams/results', [TransferExportController::class, 'examResults']);
    Route::get('slider-images', [TransferExportController::class, 'sliderImages']);
    Route::get('committees', [TransferExportController::class, 'committees']);
    Route::get('governing-body', [TransferExportController::class, 'governingBody']);
    Route::get('options', [TransferExportController::class, 'options']);
    Route::get('galleries', [GallerySyncController::class, 'index']);
});
