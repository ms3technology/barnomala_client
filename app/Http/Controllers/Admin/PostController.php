<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function index(Request $request)
    {
        $tab = $request->string('tab')->toString();
        $query = Post::with('artifacts')->latest('published_at')->latest('id');

        if ($tab !== '' && array_key_exists($tab, Post::POST_TYPES)) {
            $query->where('type', $tab);
        }

        return view('admin.posts.index', [
            'posts' => $query->paginate(15)->withQueryString(),
            'tab' => $tab ?: 'all',
            'types' => $this->types(),
            'postTypes' => Post::POST_TYPES,
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.posts.form', [
            'post' => new Post([
                'type' => $request->string('type')->toString() ?: Post::NOTICE,
                'is_active' => true,
                'published_at' => now()->toDateString(),
            ]),
            'types' => $this->types(),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->applyLexicalPayload($this->validatePost($request));
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_urgent'] = $request->boolean('is_urgent');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $post = Post::create($validated);
        $this->storeFiles($request, $post);

        return redirect()->route('admin.posts.index', ['tab' => $post->type])
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        $post->load('artifacts');

        return view('admin.posts.form', [
            'post' => $post,
            'types' => $this->types(),
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->applyLexicalPayload($this->validatePost($request), $post);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_urgent'] = $request->boolean('is_urgent');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if (isset($post->image_json['path'])) {
                Storage::disk('public')->delete($post->image_json['path']);
            }

            $path = $this->imageService->convertToWebp($request->file('image'), 'news/images');
            $validated['image_json'] = ['url' => Storage::url($path), 'path' => $path];
        }

        $post->update($validated);
        $this->renameArtifacts($request, $post);
        $this->deleteArtifacts($request, $post);
        $this->storeFiles($request, $post);

        return redirect()->route('admin.posts.index', ['tab' => $post->type])
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if (isset($post->image_json['path'])) {
            Storage::disk('public')->delete($post->image_json['path']);
        }

        foreach ($post->artifacts as $artifact) {
            Storage::disk('public')->delete($artifact->file_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }

    private function validatePost(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys($this->types()))],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'content_lexical' => ['nullable', 'string'],
            'class_label' => ['nullable', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
            'artifacts' => ['nullable', 'array'],
            'artifacts.*' => ['file', 'max:20480'],
            'delete_artifacts' => ['nullable', 'array'],
            'artifact_names' => ['nullable', 'array'],
            'artifact_names.*' => ['nullable', 'string', 'max:255'],
            'artifact_file_names' => ['nullable', 'array'],
            'artifact_file_names.*' => ['nullable', 'string', 'max:255'],
        ]);

        unset($validated['image'], $validated['artifacts'], $validated['delete_artifacts'], $validated['artifact_names'], $validated['artifact_file_names']);

        return $validated;
    }

    /**
     * Merge the lexical JSON payload into the validated dataset:
     * - decode `content_lexical` into a structured array (model cast handles persistence)
     * - derive `content` (the plain-text mirror) from it so downstream
     *   read-only renderers keep working
     */
    private function applyLexicalPayload(array $payload, ?Post $post = null): array
    {
        $raw = $payload['content_lexical'] ?? null;

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
            $payload['content_lexical'] = $decoded;
            $payload['content'] = $this->extractLexicalPlainText($decoded) ?: ($payload['content'] ?? '');
        } else {
            // Empty / invalid lexical payload: don't clobber existing data on update.
            if ($post !== null) {
                unset($payload['content_lexical']);
            } else {
                $payload['content_lexical'] = null;
            }
        }

        return $payload;
    }

    /**
     * Walk a Lexical state tree and concatenate leaf text nodes. Block-level
     * nodes gain a soft paragraph break so the legacy `content` column
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

    private function storeFiles(Request $request, Post $post): void
    {
        if (!$request->hasFile('artifacts')) {
            return;
        }

        $directory = match ($post->type) {
            Post::NOTICE => 'notices/artifacts',
            Post::NEWS => 'news/artifacts',
            default => 'downloads',
        };

        $names = (array) $request->input('artifact_file_names', []);

        foreach ($request->file('artifacts') as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $displayName = trim((string) ($names[$index] ?? ''));
            $originalName = $file->getClientOriginalName();
            $fileName = $displayName !== '' ? $displayName : $originalName;

            $path = $file->store($directory, 'public');
            $post->artifacts()->create([
                'file_path' => $path,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    private function deleteArtifacts(Request $request, Post $post): void
    {
        foreach ((array) $request->input('delete_artifacts', []) as $artifactId => $shouldDelete) {
            if (!$shouldDelete) {
                continue;
            }

            $artifact = $post->artifacts()->find($artifactId);
            if ($artifact) {
                Storage::disk('public')->delete($artifact->file_path);
                $artifact->delete();
            }
        }
    }

    private function renameArtifacts(Request $request, Post $post): void
    {
        $names = (array) $request->input('artifact_names', []);

        foreach ($names as $artifactId => $newName) {
            $newName = trim((string) $newName);

            if ($newName === '') {
                continue;
            }

            $artifact = $post->artifacts()->find($artifactId);
            if ($artifact && $artifact->file_name !== $newName) {
                $artifact->file_name = $newName;
                $artifact->save();
            }
        }
    }

    private function types(): array
    {
        return Post::POST_TYPES;
    }
}
