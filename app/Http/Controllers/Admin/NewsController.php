<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with('artifacts')->latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'artifacts' => 'nullable|array',
            'artifacts.*' => 'file|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news/images', 'public');
            $validated['image_json'] = [
                'url' => Storage::url($path),
                'path' => $path,
            ];
        }

        $newsItem = News::create($validated);

        if ($request->hasFile('artifacts')) {
            foreach ($request->file('artifacts') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('news/artifacts', 'public');
                    NewsArtifact::create([
                        'news_id' => $newsItem->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'artifacts' => 'nullable|array',
            'artifacts.*' => 'file|max:10240',
            'delete_artifacts' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            if (isset($news->image_json['path'])) {
                Storage::disk('public')->delete($news->image_json['path']);
            }
            $path = $request->file('image')->store('news/images', 'public');
            $validated['image_json'] = [
                'url' => Storage::url($path),
                'path' => $path,
            ];
        }

        $news->update($validated);

        if ($request->hasFile('artifacts')) {
            foreach ($request->file('artifacts') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('news/artifacts', 'public');
                    NewsArtifact::create([
                        'news_id' => $news->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        if ($request->has('delete_artifacts')) {
            foreach ($request->delete_artifacts as $artifactId) {
                $artifact = NewsArtifact::find($artifactId);
                if ($artifact) {
                    Storage::disk('public')->delete($artifact->file_path);
                    $artifact->delete();
                }
            }
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        if (isset($news->image_json['path'])) {
            Storage::disk('public')->delete($news->image_json['path']);
        }

        foreach ($news->artifacts as $artifact) {
            Storage::disk('public')->delete($artifact->file_path);
            $artifact->delete();
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News item deleted successfully.');
    }
}
