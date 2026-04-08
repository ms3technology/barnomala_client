<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTransfer\WordPressTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataTransferController extends Controller
{
    public function __construct(private readonly WordPressTransferService $transferService)
    {
    }

    public function index(Request $request): View
    {
        $exportResources = $this->transferService->getExportResources();

        $speechTransferPreview = null;
        $speechTransferError = null;
        try {
            $speechTransferPreview = $this->transferService->getSpeechTransferPreview();
        } catch (\Throwable $e) {
            $speechTransferError = $e->getMessage();
        }

        return view('admin.transfer.index', [
            'exportResources' => $exportResources,
            'speechTransferPreview' => $speechTransferPreview,
            'speechTransferError' => $speechTransferError,
        ]);
    }

    public function transferSpeeches(): RedirectResponse
    {
        try {
            $result = $this->transferService->transferSpeechesFromWordPress();

            return redirect()
                ->route('admin.transfer.index')
                ->with('success', sprintf(
                    'Speech transfer completed. Created: %d, Updated: %d, Skipped: %d, Options updated: %d',
                    $result['created'],
                    $result['updated'],
                    $result['skipped'],
                    $result['options_updated']
                ));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.transfer.index')
                ->withErrors(['transfer' => $e->getMessage()]);
        }
    }
}
