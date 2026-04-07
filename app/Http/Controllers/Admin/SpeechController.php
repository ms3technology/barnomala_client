<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speech;
use Illuminate\Http\Request;

class SpeechController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $speeches = Speech::latest()->paginate(10);
        return view('admin.speeches.index', compact('speeches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.speeches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'speech' => 'required|string',
            'is_active' => 'boolean',
        ]);

        Speech::create($validated);

        return redirect()->route('admin.speeches.index')
            ->with('success', 'Speech created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Speech $speech)
    {
        return view('admin.speeches.show', compact('speech'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Speech $speech)
    {
        return view('admin.speeches.edit', compact('speech'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speech $speech)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'speech' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $speech->update($validated);

        return redirect()->route('admin.speeches.index')
            ->with('success', 'Speech updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speech $speech)
    {
        $speech->delete();

        return redirect()->route('admin.speeches.index')
            ->with('success', 'Speech deleted successfully.');
    }
}
