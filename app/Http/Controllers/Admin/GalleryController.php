<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gallery::orderBy('date', 'desc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        $items = $query->paginate(24)->withQueryString();
        $categories = Gallery::whereNotNull('category')->distinct()->pluck('category');
        $years = Gallery::whereNotNull('date')
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.gallery.index', compact('items', 'categories', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->type === 'photo' && $request->has('titles')) {
            $validated = $request->validate([
                'type' => 'required|in:photo,video',
                'category' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'description' => 'nullable|string',
                'titles.*' => 'required|string|max:255',
                'images.*' => 'required|image|max:2048',
            ]);

            $titles = $request->input('titles');
            $images = $request->file('images');

            foreach ($images as $index => $image) {
                $path = $image->store('gallery/photos', 'public');
                Gallery::create([
                    'type' => 'photo',
                    'title' => $titles[$index] ?? 'Untitled',
                    'category' => $request->category,
                    'date' => $request->date,
                    'description' => $request->description,
                    'image_path' => $path,
                ]);
            }

            return redirect()->route('admin.gallery.index')
                ->with('success', count($images) . ' gallery items added successfully.');
        }

        $validated = $request->validate([
            'type' => 'required|in:photo,video',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'image' => 'required_if:type,photo|nullable|image|max:2048',
            'video_url' => 'nullable|url|max:255',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480', // 20MB limit
            'description' => 'nullable|string',
        ]);

        if ($request->type === 'photo' && $request->hasFile('image')) {
            $path = $request->file('image')->store('gallery/photos', 'public');
            $validated['image_path'] = $path;
        } elseif ($request->type === 'video') {
            if ($request->hasFile('video_file')) {
                $path = $request->file('video_file')->store('gallery/videos', 'public');
                $validated['video_path'] = $path;
                $validated['video_url'] = null;
            }
        }

        Gallery::create($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Gallery::findOrFail($id);
        return view('admin.gallery.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Gallery::findOrFail($id);
        return view('admin.gallery.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Gallery::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:photo,video',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url|max:255',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480', // 20MB limit
            'description' => 'nullable|string',
        ]);

        if ($request->type === 'photo' && $request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            if ($item->video_path) {
                Storage::disk('public')->delete($item->video_path);
                $validated['video_path'] = null;
            }
            $path = $request->file('image')->store('gallery/photos', 'public');
            $validated['image_path'] = $path;
            $validated['video_url'] = null;
        } elseif ($request->type === 'video') {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
                $validated['image_path'] = null;
            }

            if ($request->hasFile('video_file')) {
                if ($item->video_path) {
                    Storage::disk('public')->delete($item->video_path);
                }
                $path = $request->file('video_file')->store('gallery/videos', 'public');
                $validated['video_path'] = $path;
                $validated['video_url'] = null;
            } elseif ($request->filled('video_url')) {
                if ($item->video_path) {
                    Storage::disk('public')->delete($item->video_path);
                    $validated['video_path'] = null;
                }
            }
        }

        $item->update($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Gallery::findOrFail($id);
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        if ($item->video_path) {
            Storage::disk('public')->delete($item->video_path);
        }
        $item->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }
}
