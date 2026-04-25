<?php

use App\Http\Controllers\Api\OptionSyncController;
use App\Http\Controllers\Api\TeacherSyncController;
use App\Http\Controllers\Api\StaffSyncController;
use App\Http\Controllers\Api\CommitteeSyncController;
use App\Http\Controllers\Api\TransferExportController;
use App\Http\Controllers\Api\DataTransferController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    Route::post('options/sync', [OptionSyncController::class, 'sync']);
    Route::post('teachers/sync', [TeacherSyncController::class, 'sync']);
    Route::post('staff/sync', [StaffSyncController::class, 'sync']);
    Route::post('committees/sync', [CommitteeSyncController::class, 'sync']);
    Route::post('transfer/all', [DataTransferController::class, 'transferAll'])->name('api.transfer.all');
    Route::post('setup/default-website', [DataTransferController::class, 'setupDefaultWebsite'])->name('api.setup.default-website');
});

Route::prefix('barnomala/v1')->group(function () {
    Route::get('students', [TransferExportController::class, 'students']);
    Route::get('student/enrollments', [TransferExportController::class, 'studentEnrollments']);
    Route::get('subjects', [TransferExportController::class, 'subjects']);
    Route::get('teachers', [TransferExportController::class, 'teachers']);
    Route::get('exams', [TransferExportController::class, 'exams']);
    Route::get('exams/schedules', [TransferExportController::class, 'examSchedules']);
    Route::get('exams/results', [TransferExportController::class, 'examResults']);
    Route::get('slider-images', [TransferExportController::class, 'sliderImages']);
    Route::get('committees', [TransferExportController::class, 'committees']);
    Route::get('governing-body', [TransferExportController::class, 'governingBody']);
    Route::get('options', [TransferExportController::class, 'options']);
});