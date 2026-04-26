<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GallerySyncController extends Controller
{
    public function sync(Request $request)
    {
        $galleriesData = $request->input('galleries', []);
        
        if (!is_array($galleriesData)) {
            $galleriesData = json_decode($request->getContent(), true)['galleries'] ?? [];
        }

        $summary = [
            'updated' => 0,
            'deleted' => 0,
            'failed' => 0
        ];

        try {
            DB::beginTransaction();
            foreach ($galleriesData as $gallery) {
                if (!isset($gallery['id'])) {
                    $summary['failed']++;
                    continue;
                }

                $id = $gallery['id'];
                
                // If body has only id, delete it
                if (count($gallery) === 1) {
                    $deleted = Gallery::where('id', $id)->delete();
                    if ($deleted) $summary['deleted']++;
                    continue;
                }

                // Map data from request to gallery model attributes
                $data = [
                    'type' => $gallery['type'] ?? Gallery::TYPE_PHOTO,
                    'title' => $gallery['title'] ?? null,
                    'category' => $gallery['category'] ?? null,
                    'date' => $gallery['date'] ?? null,
                    'image_path' => $gallery['image_path'] ?? null,
                    'video_url' => $gallery['video_url'] ?? null,
                    'video_path' => $gallery['video_path'] ?? null,
                    'description' => $gallery['description'] ?? null,
                ];

                Gallery::updateOrCreate(
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
            Log::error('Gallery Sync Error: ' . $e->getMessage());
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
        $galleries = Gallery::orderBy('date', 'desc')->paginate($perPage);
        return response()->json([
            'status' => 'success',
            'data' => $galleries->items(),
            'pagination' => [
                'current_page' => $galleries->currentPage(),
                'per_page' => $galleries->perPage(),
                'total' => $galleries->total(),
                'last_page' => $galleries->lastPage(),
            ]
        ]);
    }
}
