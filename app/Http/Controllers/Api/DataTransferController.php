<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataTransfer\WordPressTransferService;
use Illuminate\Http\JsonResponse;

class DataTransferController extends Controller
{
    public function __construct(
        private readonly WordPressTransferService $transferService
    )
    {
    }

    public function transferAll(): JsonResponse
    {
        $actions = [
            'speeches' => fn (): array => $this->transferService->transferSpeechesFromWordPress(),
            'sliders' => fn (): array => $this->transferService->transferSliderImagesFromWordPress(),
            'galleries' => fn (): array => $this->transferService->transferGalleriesFromWordPress(),
            'notices' => fn (): array => $this->transferService->transferNoticesFromWordPress(),
            'news' => fn (): array => $this->transferService->transferNewsFromWordPress(),
        ];

        $results = [];
        $successfulActions = 0;
        $failedActions = 0;

        foreach ($actions as $name => $handler) {
            try {
                $results[$name] = [
                    'status' => 'success',
                    'result' => $handler(),
                ];
                $successfulActions++;
            } catch (\Throwable $e) {
                $results[$name] = [
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
                $failedActions++;
            }
        }

        $allSucceeded = $failedActions === 0;

        return response()->json([
            'status' => $allSucceeded ? 'success' : 'partial_failure',
            'message' => $allSucceeded
                ? 'All transfer actions completed successfully.'
                : 'Transfer completed with one or more failed actions.',
            'summary' => [
                'total_actions' => count($actions),
                'successful_actions' => $successfulActions,
                'failed_actions' => $failedActions,
            ],
            'results' => $results,
        ], $allSucceeded ? 200 : 207);
    }

    public function setupDefaultWebsite(): JsonResponse
    {
        try {
            $result = $this->transferService->seedWebsiteDefaults();

            return response()->json([
                'status' => 'success',
                'message' => 'Default website options and sample data have been set successfully.',
                'result' => $result,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to set default website data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}