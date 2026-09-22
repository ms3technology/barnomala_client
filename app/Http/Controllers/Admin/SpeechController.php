<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speech;
use Illuminate\Http\Request;

class SpeechController extends Controller
{
    protected $imageService;

    public function __construct(\App\Services\ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rows = [1, 2, 3];
        $speeches = Speech::query()
            ->orderBy('row_index')
            ->orderBy('column_index')
            ->orderBy('id')
            ->get()
            ->groupBy('row_index');
        
        return view('admin.speeches.index', compact('speeches', 'rows'));
    }

    public function storeQuick(Request $request)
    {
        $request->validate([
            'row_index' => 'required|integer|between:1,3',
            'column_index' => 'required|integer|between:1,3',
        ]);

        $existing = Speech::where('row_index', $request->row_index)
            ->where('column_index', $request->column_index)
            ->first();

        if ($existing) {
            return redirect()->route('admin.speeches.edit', $existing);
        }

        $speech = Speech::create([
            'name' => 'New Speaker',
            'title' => 'Speech Title',
            'speech' => 'Enter speech here...',
            'row_index' => $request->row_index,
            'column_index' => $request->column_index,
            'colspan' => 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.speeches.edit', $speech);
    }

    public function updateRowConfig(Request $request)
    {
        $request->validate([
            'row_index' => 'required|integer|between:1,3',
            'config' => 'required|string|in:1 item,2 items,3 items',
        ]);

        // Option::updateOrCreate(['key' => "speech.row.{$request->row_index}.config"], ['value' => $request->config]);
        // For now, we will just use a generic way or assume user can store it in options table if it exists.
        // Given your workspace has OptionsSeeder.php and Option.php model.
        \App\Models\Option::set("speech.row.{$request->row_index}.config", $request->config);

        return back()->with('success', 'Row configuration updated.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.speeches.form', [
            'speech' => new Speech(['is_active' => true]),
            'isEditing' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'speech' => 'nullable|string',
            'speech_lexical' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated = $this->applyLexicalPayload($validated);

        $imageJson = null;
        if ($request->hasFile('image')) {
            $path = $this->imageService->convertToWebp($request->file('image'), 'speeches');
            $imageJson = [
                'url' => \Illuminate\Support\Facades\Storage::url($path),
                'path' => $path,
                'name' => $request->file('image')->getClientOriginalName(),
            ];
            $validated['image_json'] = $imageJson;
        }

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
        return view('admin.speeches.form', [
            'speech' => $speech,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speech $speech)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'speech' => 'nullable|string',
            'speech_lexical' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated = $this->applyLexicalPayload($validated, $speech);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($speech->image_json && isset($speech->image_json['path'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($speech->image_json['path']);
            }

            $path = $this->imageService->convertToWebp($request->file('image'), 'speeches');
            $validated['image_json'] = [
                'url' => \Illuminate\Support\Facades\Storage::url($path),
                'path' => $path,
                'name' => $request->file('image')->getClientOriginalName(),
            ];
        }

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

    /**
     * Merge the lexical JSON payload into the validated dataset:
     * - decode `speech_lexical` into a structured array (model cast handles persistence)
     * - derive `speech` (the plain-text mirror) from it so downstream
     *   read-only renderers keep working
     */
    private function applyLexicalPayload(array $payload, ?Speech $speech = null): array
    {
        $raw = $payload['speech_lexical'] ?? null;

        if ($raw !== null) {
            $raw = trim((string) $raw);
        }

        $decoded = null;
        if ($raw !== null && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                $decoded = null;
            }
        }

        if ($decoded !== null) {
            $payload['speech_lexical'] = $decoded;
            $payload['speech'] = $this->extractLexicalPlainText($decoded) ?: ($payload['speech'] ?? '');
        } else {
            // Empty / invalid lexical payload: don't clobber existing data on update.
            if ($speech !== null) {
                unset($payload['speech_lexical']);
            } else {
                $payload['speech_lexical'] = null;
            }
        }

        return $payload;
    }

    /**
     * Walk a Lexical state tree and concatenate leaf text nodes. Block-level
     * nodes gain a soft paragraph break so the legacy `speech` column
     * stays readable when editors use multiple blocks.
     */
    private function extractLexicalPlainText(array $node): string
    {
        $text = '';

        if (isset($node['text']) && is_string($node['text'])) {
            $text .= $node['text'];
        }

        if (isset($node['children']) && is_array($node['children'])) {
            foreach ($node['children'] as $child) {
                if (is_array($child)) {
                    $text .= $this->extractLexicalPlainText($child);
                }
            }
        }

        $type = $node['type'] ?? null;
        if (in_array($type, ['paragraph', 'heading', 'quote', 'listitem'], true) && $text !== '') {
            $text .= "\n\n";
        }

        return $text;
    }
}
