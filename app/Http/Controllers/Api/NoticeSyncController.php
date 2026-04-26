<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoticeSyncController extends Controller
{
    public function sync(Request $request)
    {
        $noticesData = $request->input('notices', []);

        if (!is_array($noticesData)) {
            $noticesData = json_decode($request->getContent(), true)['notices'] ?? [];
        }
        
        // Preload all notices in one query (avoid N+1)
        $ids = collect($noticesData)->pluck('id')->filter()->all();
        $existingNotices = Notice::whereIn('id', $ids)->get()->keyBy('id');

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($noticesData as $notice) {
                // Find existing notice by ID
                $noticeModel = $existingNotices[$notice['id']] ?? null;

                // If only ID is provided → delete
                if (count($notice) === 1) {
                    if ($noticeModel) {
                        $noticeModel->artifacts()->delete();
                        $noticeModel->delete();
                        $summary['deleted']++;
                    }
                    continue;
                }

                // Prepare data once
                $data = [
                    'title' => $notice['title'] ?? null,
                    'content' => $notice['content'] ?? null,
                    'published_at' => $notice['published_at'] ?? null,
                    'is_active' => $notice['is_active'] ?? true,
                    'is_urgent' => $notice['is_urgent'] ?? false,
                ];

                if ($noticeModel) {
                    $noticeModel->update($data);
                } else {
                    $noticeModel = Notice::create($data);
                }

                // Sync artifacts if provided
                if (!empty($notice['artifacts']) && is_array($notice['artifacts'])) {
                    $this->syncArtifacts($noticeModel, $notice['artifacts']);
                }

                $summary['updated']++;
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Notice Sync Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function syncArtifacts(Notice $notice, array $artifacts)
    {
        $processedIds = [];

        foreach ($artifacts as $artifact) {
            // If only id is provided, delete the artifact
            if (isset($artifact['id']) && count($artifact) === 1) {
                NoticeArtifact::where('id', $artifact['id'])
                    ->where('notice_id', $notice->id)
                    ->delete();
                continue;
            }

            // Upsert artifact by id
            if (isset($artifact['id'])) {
                $artifactModel = NoticeArtifact::updateOrCreate(
                    ['id' => $artifact['id']],
                    [
                        'notice_id' => $notice->id,
                        'file_path' => $artifact['file_path'] ?? null,
                        'file_name' => $artifact['file_name'] ?? null,
                        'file_type' => $artifact['file_type'] ?? null,
                        'file_size' => $artifact['file_size'] ?? null,
                    ]
                );
                $processedIds[] = $artifactModel->id;
            } else {
                // Create new artifact without id
                $artifactModel = NoticeArtifact::create([
                    'notice_id' => $notice->id,
                    'file_path' => $artifact['file_path'] ?? null,
                    'file_name' => $artifact['file_name'] ?? null,
                    'file_type' => $artifact['file_type'] ?? null,
                    'file_size' => $artifact['file_size'] ?? null,
                ]);
                $processedIds[] = $artifactModel->id;
            }
        }
    }

    public function index()
    {
        $perPage = request()->get('per_page', 15);
        $notices = Notice::with('artifacts')->orderBy('published_at', 'desc')->paginate($perPage);
        return response()->json([
            'status' => 'success',
            'data' => $notices->items(),
            'pagination' => [
                'current_page' => $notices->currentPage(),
                'per_page' => $notices->perPage(),
                'total' => $notices->total(),
                'last_page' => $notices->lastPage(),
            ]
        ]);
    }
}
