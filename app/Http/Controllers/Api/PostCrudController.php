<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostArtifact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PostCrudController extends Controller
{
    /**
     * Whitelist of columns callers are allowed to write on a Post. Mirrors
     * `Post::$fillable`; keeping it explicit lets us strip unknown keys
     * (like `_method` leftovers) without leaning on Eloquent mass-assignment.
     */
    private const WRITABLE_FIELDS = [
        'type',
        'legacy_id',
        'title',
        'content',
        'class_label',
        'published_at',
        'image_json',
        'is_active',
        'is_urgent',
        'is_featured',
        'sort_order',
    ];

    /**
     * Pull a Post payload from the request, restricted to WRITABLE_FIELDS.
     * Non-fillable keys (e.g. `id`, `_token`, raw `artifacts`) are dropped.
     */
    private function extractPostData(array $payload): array
    {
        return array_intersect_key($payload, array_flip(self::WRITABLE_FIELDS));
    }

    private function syncArtifacts(Post $post, array $artifacts): void
    {
        foreach ($artifacts as $artifact) {
            if (!is_array($artifact)) {
                continue;
            }

            // Body contains only `id` → drop it.
            $isArtifactDeletion = isset($artifact['id'])
                && count(array_diff_key($artifact, array_flip(['id']))) === 0;

            if ($isArtifactDeletion) {
                PostArtifact::query()
                    ->where('post_id', $post->id)
                    ->where('id', $artifact['id'])
                    ->delete();
                continue;
            }

            $payload = [
                'post_id' => $post->id,
                'file_path' => $artifact['file_path'] ?? null,
                'file_name' => $artifact['file_name'] ?? null,
                'file_type' => $artifact['file_type'] ?? null,
                'file_size' => $artifact['file_size'] ?? null,
            ];

            if (isset($artifact['id'])) {
                PostArtifact::updateOrCreate(
                    ['id' => $artifact['id'], 'post_id' => $post->id],
                    $payload
                );
            } else {
                PostArtifact::create($payload);
            }
        }
    }

    /* ---------------------------------------------------------------- */
    // ---------- read endpoints (GET /api/v1/posts) -------------------
    /* ---------------------------------------------------------------- */

    /**
     * GET /api/v1/posts
     *
     * Paginated list across every post type.
     *
     * Query params (all optional):
     *   - type        : filter by post type (must be in Post::POST_TYPES).
     *                   Supports comma-separated lists, e.g. "notice,news".
     *                   Use `download` as a shortcut for every non-news,
     *                   non-notice type (see Post::downloadTypes()).
     *   - is_active   : 1 / 0 / true / false.
     *   - search      : case-insensitive substring on `title` (and `content`
     *                   for download types).
     *   - class_label : exact match (download types).
     *   - featured    : 1 / 0 (news only).
     *   - urgent      : 1 / 0 (notice only).
     *   - per_page    : items per page, default 15, max 100.
     *   - page        : page number, default 1.
     *   - order_by    : "published_at" (default), "sort_order", or "created_at".
     *   - direction   : "desc" (default) or "asc".
     */
    public function index(Request $request)
    {
        $allowedTypes = array_keys(Post::POST_TYPES);
        $downloadTypes = array_keys(Post::downloadTypes());

        $validated = $request->validate([
            'type' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'search' => ['nullable', 'string', 'max:255'],
            'class_label' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable'],
            'urgent' => ['nullable'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'order_by' => ['nullable', 'string', Rule::in(['published_at', 'sort_order', 'created_at', 'updated_at'])],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ]);

        $types = $this->resolveTypes($validated['type'] ?? null, $allowedTypes);

        if ($validated['type'] ?? null) {
            $unknown = array_diff(
                array_map('trim', explode(',', $validated['type'])),
                array_merge($allowedTypes, ['download'])
            );
            if (!empty($unknown)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown post type(s): ' . implode(', ', $unknown),
                    'allowed' => $allowedTypes,
                ], 422);
            }

            // `download` is a virtual type covering every non-news,
            // non-notice post. Expand it to the concrete list so the
            // downstream `whereIn('type', $types)` filters correctly.
            $types = $this->expandDownloadAlias($types, $downloadTypes);
        }

        $perPage = (int) ($validated['per_page'] ?? 15);
        $orderBy = $validated['order_by'] ?? 'published_at';
        $direction = $validated['direction'] ?? 'desc';

        $query = Post::query()->with('artifacts');

        if (!empty($types)) {
            $query->whereIn('type', $types);
        }

        if (array_key_exists('is_active', $validated)) {
            $query->where('is_active', $this->toBool($validated['is_active']));
        }

        if (!empty($validated['search'])) {
            $needle = '%' . $validated['search'] . '%';
            $query->where(function ($q) use ($needle) {
                $q->where('title', 'like', $needle)
                  ->orWhere('content', 'like', $needle);
            });
        }

        if (!empty($validated['class_label'])) {
            $query->where('class_label', $validated['class_label']);
        }

        if (array_key_exists('featured', $validated)) {
            $query->where('is_featured', $this->toBool($validated['featured']));
        }

        if (array_key_exists('urgent', $validated)) {
            $query->where('is_urgent', $this->toBool($validated['urgent']));
        }

        $query->orderBy($orderBy, $direction)
              ->orderBy('id', 'desc');

        $posts = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $posts->items(),
            'meta' => [
                'allowed_types' => $allowedTypes,
                'type_labels' => Post::POST_TYPES,
            ],
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/posts/{type}
     *
     * Paginated list scoped to a single post type.
     * Accepts the same query params as `index` minus `type`.
     */
    public function indexByType(Request $request, string $type)
    {
        if (!in_array($type, array_keys(Post::POST_TYPES), true)) {
            return response()->json([
                'status' => 'error',
                'message' => "Unknown post type: {$type}",
                'allowed' => array_keys(Post::POST_TYPES),
            ], 404);
        }

        $request->merge(['type' => $type]);

        return $this->index($request);
    }

    /**
     * GET /api/v1/posts/{id}
     *
     * `id` is the local primary key. Post type is read from the record.
     */
    public function show(Request $request, string $id)
    {
        $post = Post::query()
            ->with('artifacts')
            ->where('id', $id)
            ->first();

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => "Post not found: id={$id}",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $post,
        ]);
    }

    /* ---------------------------------------------------------------- */
    // ---------- write endpoints (POST/PUT/DELETE /api/v1/posts) -------
    /* ---------------------------------------------------------------- */

    /**
     * POST /api/v1/posts
     *
     * Create a single post. The `type` is taken from the request body so
     * callers can target any Post type without separate URL segments. All
     * Post columns are accepted regardless of `type`; only `type`, `title`
     * and `published_at` carry validation rules.
     *
     * Optional `artifacts` array creates attached files in the same call.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        try {
            DB::beginTransaction();

            $data = $this->extractPostData($validated);
            $post = Post::create($data);

            if (!empty($validated['artifacts']) && is_array($validated['artifacts'])) {
                $this->syncArtifacts($post, $validated['artifacts']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Post created.',
                'data' => $post->fresh('artifacts'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Post Create Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Create failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT/PATCH /api/v1/posts/{id}
     *
     * Update a single post by its local primary `id`. Body fields are
     * optional — only the keys present are updated. Any Post column may
     * be targeted; `type` may also be reassigned by sending it in the body.
     *
     * Pass `artifacts` to upsert attachments against the post; omit the
     * key to leave existing artifacts untouched. To delete a single
     * attachment, include its `id` in the array as `{ "id": 123 }`.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::query()->where('id', $id)->first();

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => "Post not found: id={$id}",
            ], 404);
        }

        $validated = $request->validate($this->validationRules(partial: true));

        try {
            DB::beginTransaction();

            $data = $this->extractPostData($validated);

            if (!empty($data)) {
                $post->fill($data)->save();
            }

            if (array_key_exists('artifacts', $validated) && is_array($validated['artifacts'])) {
                $this->syncArtifacts($post, $validated['artifacts']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated.',
                'data' => $post->fresh('artifacts'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Post Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/v1/posts/{id}
     *
     * Delete a single post (by local primary key) and all of its artifacts.
     * The post's `type` is read from the row itself, no URL segment needed.
     */
    public function destroy(Request $request, string $id)
    {
        $post = Post::query()->where('id', $id)->first();

        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => "Post not found: id={$id}",
            ], 404);
        }

        try {
            DB::beginTransaction();
            $post->artifacts()->delete();
            $post->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted.',
                'deleted' => [
                    'type' => $post->type,
                    'id' => $post->id,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Post Delete Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Delete failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validation rules for create (`partial=false`) and update
     * (`partial=true`) payloads. Since write endpoints now accept any
     * column regardless of post `type`, all Post fields are listed here
     * with permissive rules. `type` and `title` are required on create;
     * on update `type` is optional but, if present, must be a known value.
     */
    private function validationRules(bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        $nullable = 'nullable';
        $allowedTypes = array_keys(Post::POST_TYPES);

        return [
            // Post columns — all optional/nullable, no per-type gating.
            'type' => [$required, 'string', Rule::in($allowedTypes)],
            'legacy_id' => [$nullable, 'integer'],
            'title' => [$required, 'string', 'max:255'],
            'content' => [$nullable, 'string'],
            'class_label' => [$nullable, 'string', 'max:255'],
            'published_at' => [$nullable, 'date'],
            'image_json' => [$nullable, 'array'],
            'image_json.url' => [$nullable, 'string', 'max:500'],
            'is_active' => [$nullable, 'boolean'],
            'is_urgent' => [$nullable, 'boolean'],
            'is_featured' => [$nullable, 'boolean'],
            'sort_order' => [$nullable, 'integer'],

            // Artifacts: same shape as before.
            'artifacts' => [$nullable, 'array'],
            'artifacts.*.id' => [$nullable, 'integer'],
            'artifacts.*.file_path' => [$nullable, 'string', 'max:500'],
            'artifacts.*.file_name' => [$nullable, 'string', 'max:255'],
            'artifacts.*.file_type' => [$nullable, 'string', 'max:100'],
            'artifacts.*.file_size' => [$nullable, 'integer', 'min:0'],
        ];
    }

    /* ---------------------------------------------------------------- */
    // ---------- read helpers ------------------------------------------
    /* ---------------------------------------------------------------- */

    /**
     * Parse the `type` query param into an array. Supports comma-separated
     * values. Returns `null` when no filter was requested (meaning: all
     * types allowed).
     */
    private function resolveTypes(?string $raw, array $allowed): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $types = array_values(array_filter(array_map('trim', explode(',', $raw))));
        $types = array_values(array_intersect($types, $allowed));

        return $types ?: null;
    }

    /**
     * Replace the virtual `download` token with the full list of download
     * types so the rest of the query stays unaware of the alias.
     *
     * @param  array<int, string>|null  $types
     * @param  array<int, string>       $downloadTypes
     * @return array<int, string>|null
     */
    private function expandDownloadAlias(?array $types, array $downloadTypes): ?array
    {
        if (empty($types) || !in_array('download', $types, true)) {
            return $types;
        }

        $remaining = array_values(array_diff($types, ['download']));

        return array_values(array_unique(array_merge($remaining, $downloadTypes)));
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}