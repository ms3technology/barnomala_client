<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataTransfer\WordPressExportService;
use App\Services\DataTransfer\WordPressTransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataTransferController extends Controller
{
    public function __construct(
        private readonly WordPressTransferService $transferService,
        private readonly WordPressExportService $exportService
    )
    {
    }

    public function index(Request $request): View
    {
        $exportResources = $this->exportService->getExportResources();

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
            'locks' => [
                'speeches' => $this->transferService->isLocked('speeches'),
                'sliders' => $this->transferService->isLocked('sliders'),
                'galleries' => $this->transferService->isLocked('galleries'),
                'notices' => $this->transferService->isLocked('notices'),
                'news' => $this->transferService->isLocked('news'),
            ]
        ]);
    }

    public function toggleLock(Request $request): RedirectResponse
    {
        $section = $request->input('section');
        $lock = $request->boolean('lock');

        if ($lock) {
            $this->transferService->lock($section);
        } else {
            $this->transferService->unlock($section);
        }

        return redirect()->back()->with('success', ucfirst($section) . ' transfer ' . ($lock ? 'locked' : 'unlocked') . '.');
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

    public function transferSliders(): RedirectResponse
    {
        try {
            $result = $this->transferService->transferSliderImagesFromWordPress();

            return redirect()
                ->route('admin.transfer.index')
                ->with('success', sprintf(
                    'Slider image transfer completed. Transferred: %d/%d, Options updated: %s',
                    $result['transferred'],
                    $result['total_source'],
                    $result['option_updated'] ? 'Yes' : 'No'
                ));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.transfer.index')
                ->withErrors(['transfer' => $e->getMessage()]);
        }
    }

    public function transferGalleries(): RedirectResponse
    {
        try {
            $result = $this->transferService->transferGalleriesFromWordPress();

            return redirect()
                ->route('admin.transfer.index')
                ->with('success', sprintf(
                    'Gallery transfer completed. Created: %d, Updated: %d, Skipped (no image): %d, Image download failed: %d, Source rows: %d',
                    $result['created'],
                    $result['updated'],
                    $result['skipped_no_image'],
                    $result['failed_image_download'],
                    $result['total_source']
                ));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.transfer.index')
                ->withErrors(['transfer' => $e->getMessage()]);
        }
    }

    public function transferNotices(): RedirectResponse
    {
        try {
            $result = $this->transferService->transferNoticesFromWordPress();

            return redirect()
                ->route('admin.transfer.index')
                ->with('success', sprintf(
                    'Notice transfer completed. Created: %d, Updated: %d, Artifacts downloaded: %d, Artifact failed: %d, Source rows: %d',
                    $result['created'],
                    $result['updated'],
                    $result['artifact_downloaded'],
                    $result['artifact_failed'],
                    $result['total_source']
                ));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.transfer.index')
                ->withErrors(['transfer' => $e->getMessage()]);
        }
    }

    public function transferNews(): RedirectResponse
    {
        try {
            $result = $this->transferService->transferNewsFromWordPress();

            return redirect()
                ->route('admin.transfer.index')
                ->with('success', sprintf(
                    'News transfer completed. Created: %d, Updated: %d, Artifacts downloaded: %d, Artifact failed: %d, Source rows: %d',
                    $result['created'],
                    $result['updated'],
                    $result['artifact_downloaded'],
                    $result['artifact_failed'],
                    $result['total_source']
                ));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.transfer.index')
                ->withErrors(['transfer' => $e->getMessage()]);
        }
    }
}
