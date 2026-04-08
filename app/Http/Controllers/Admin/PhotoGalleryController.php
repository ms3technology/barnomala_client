<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $photos = PhotoGallery::orderBy('date', 'desc')->paginate(15);
        return view('admin.photo-gallery.index', compact('photos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.photo-gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'image' => 'required|image|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('photo-gallery', 'public');
            $validated['image_path'] = $path;
        }

        PhotoGallery::create($validated);

        return redirect()->route('admin.photo-gallery.index')
            ->with('success', 'Photo added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $photo = PhotoGallery::findOrFail($id);
        return view('admin.photo-gallery.show', compact('photo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $photo = PhotoGallery::findOrFail($id);
        return view('admin.photo-gallery.edit', compact('photo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $photo = PhotoGallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($photo->image_path) {
                Storage::disk('public')->delete($photo->image_path);
            }
            $path = $request->file('image')->store('photo-gallery', 'public');
            $validated['image_path'] = $path;
        }

        $photo->update($validated);

        return redirect()->route('admin.photo-gallery.index')
            ->with('success', 'Photo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $photo = PhotoGallery::findOrFail($id);
        if ($photo->image_path) {
            Storage::disk('public')->delete($photo->image_path);
        }
        $photo->delete();

        return redirect()->route('admin.photo-gallery.index')
            ->with('success', 'Photo deleted successfully.');
    }
}
