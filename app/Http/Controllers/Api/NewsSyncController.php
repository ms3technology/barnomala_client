<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsSyncController extends Controller
{
    public function sync(Request $request)
    {
        $newsData = $request->input('news', []);
        
        if (!is_array($newsData)) {
            $newsData = json_decode($request->getContent(), true)['news'] ?? [];
        }

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($newsData as $news) {
                if (!isset($news['id'])) {
                    $summary['failed']++;
                    continue;
                }

                $id = $news['id'];
                
                // If body has only id, delete it (and its artifacts)
                if (count($news) === 1) {
                    $newsModel = News::find($id);
                    if ($newsModel) {
                        // Delete associated artifacts first
                        $newsModel->artifacts()->delete();
                        $newsModel->delete();
                        $summary['deleted']++;
                    }
                    continue;
                }

                // Map data from request to news model attributes
                $data = [
                    'title' => $news['title'] ?? null,
                    'summary' => $news['summary'] ?? null,
                    'content' => $news['content'] ?? null,
                    'published_at' => $news['published_at'] ?? null,
                    'image_json' => $news['image_json'] ?? null,
                    'is_active' => $news['is_active'] ?? true,
                    'is_featured' => $news['is_featured'] ?? false,
                ];

                $newsModel = News::updateOrCreate(
                    ['id' => $id],
                    $data
                );

                // Sync artifacts if provided
                if (isset($news['artifacts']) && is_array($news['artifacts'])) {
                    $this->syncArtifacts($newsModel, $news['artifacts']);
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
            Log::error('News Sync Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function syncArtifacts(News $news, array $artifacts)
    {
        $processedIds = [];

        foreach ($artifacts as $artifact) {
            // If only id is provided, delete the artifact
            if (isset($artifact['id']) && count($artifact) === 1) {
                NewsArtifact::where('id', $artifact['id'])
                    ->where('news_id', $news->id)
                    ->delete();
                continue;
            }

            // Upsert artifact by id
            if (isset($artifact['id'])) {
                $artifactModel = NewsArtifact::updateOrCreate(
                    ['id' => $artifact['id']],
                    [
                        'news_id' => $news->id,
                        'file_path' => $artifact['file_path'] ?? null,
                        'file_name' => $artifact['file_name'] ?? null,
                        'file_type' => $artifact['file_type'] ?? null,
                        'file_size' => $artifact['file_size'] ?? null,
                    ]
                );
                $processedIds[] = $artifactModel->id;
            } else {
                // Create new artifact without id
                $artifactModel = NewsArtifact::create([
                    'news_id' => $news->id,
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
        $news = News::with('artifacts')->orderBy('published_at', 'desc')->paginate($perPage);
        return response()->json([
            'status' => 'success',
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'last_page' => $news->lastPage(),
            ]
        ]);
    }
}
