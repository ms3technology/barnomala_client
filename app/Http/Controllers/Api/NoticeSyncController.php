<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
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

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($noticesData as $notice) {
                if (!isset($notice['id'])) {
                    $summary['failed']++;
                    continue;
                }

                $id = $notice['id'];
                
                // If body has only id, delete it
                if (count($notice) === 1) {
                    $deleted = Notice::where('id', $id)->delete();
                    if ($deleted) $summary['deleted']++;
                    continue;
                }

                // Map data from request to notice model attributes
                $data = [
                    'title' => $notice['title'] ?? null,
                    'content' => $notice['content'] ?? null,
                    'published_at' => $notice['published_at'] ?? null,
                    'is_active' => $notice['is_active'] ?? true,
                    'is_urgent' => $notice['is_urgent'] ?? false,
                ];

                Notice::updateOrCreate(
                    ['id' => $id],
                    $data
                );
                
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

    public function index()
    {
        $perPage = request()->get('per_page', 15);
        $notices = Notice::orderBy('published_at', 'desc')->paginate($perPage);
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
