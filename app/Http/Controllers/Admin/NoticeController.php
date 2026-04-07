<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::with('artifacts')->latest()->paginate(10);
        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.notices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
            'artifacts' => 'nullable|array',
            'artifacts.*' => 'file|max:10240',
        ]);

        $notice = Notice::create($validated);

        // Handle artifact uploads
        if ($request->hasFile('artifacts')) {
            foreach ($request->file('artifacts') as $file) {
                // Check if file is valid before attempting to save
                if ($file->isValid()) {
                    $path = $file->store('notices/artifacts', 'public');
                    NoticeArtifact::create([
                        'notice_id' => $notice->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        return view('admin.notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
            'artifacts' => 'nullable|array',
            'artifacts.*' => 'file|max:10240',
            'delete_artifacts' => 'nullable|array',
        ]);

        $notice->update($validated);

        // Handle artifact deletions
        if ($request->has('delete_artifacts')) {
            foreach ($request->delete_artifacts as $artifactId => $shouldDelete) {
                if ($shouldDelete) {
                    $artifact = NoticeArtifact::find($artifactId);
                    if ($artifact) {
                        Storage::disk('public')->delete($artifact->file_path);
                        $artifact->delete();
                    }
                }
            }
        }

        // Handle new artifact uploads
        if ($request->hasFile('artifacts')) {
            foreach ($request->file('artifacts') as $file) {
                $path = $file->store('notices/artifacts', 'public');
                NoticeArtifact::create([
                    'notice_id' => $notice->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        // Delete associated artifacts
        foreach ($notice->artifacts as $artifact) {
            Storage::disk('public')->delete($artifact->file_path);
            $artifact->delete();
        }

        $notice->delete();

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice deleted successfully.');
    }
}
