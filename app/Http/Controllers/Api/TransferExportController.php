<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataTransfer\WordPressTransferService;
use Illuminate\Http\JsonResponse;

class TransferExportController extends Controller
{
    public function __construct(private readonly WordPressTransferService $transferService)
    {
    }

    public function students(): JsonResponse
    {
        return response()->json($this->transferService->exportStudents());
    }

    public function studentEnrollments(): JsonResponse
    {
        return response()->json($this->transferService->exportStudentEnrollments());
    }

    public function subjects(): JsonResponse
    {
        return response()->json($this->transferService->exportSubjects());
    }

    public function users(): JsonResponse
    {
        return response()->json($this->transferService->exportUsers());
    }

    public function teachers(): JsonResponse
    {
        return response()->json($this->transferService->exportTeachers());
    }

    public function exams(): JsonResponse
    {
        return response()->json($this->transferService->exportExams());
    }

    public function examSchedules(): JsonResponse
    {
        return response()->json($this->transferService->exportExamSchedules());
    }

    public function examResults(): JsonResponse
    {
        return response()->json($this->transferService->exportExamResults());
    }

    public function sliderImages(): JsonResponse
    {
        return response()->json($this->transferService->exportSliderImages());
    }

    public function committees(): JsonResponse
    {
        return response()->json($this->transferService->exportCommittees());
    }

    public function governingBody(): JsonResponse
    {
        return response()->json($this->transferService->exportGoverningBody());
    }

    public function options(): JsonResponse
    {
        return response()->json($this->transferService->exportOptions());
    }

    public function speeches(): JsonResponse
    {
        return response()->json($this->transferService->exportSpeeches());
    }
}
