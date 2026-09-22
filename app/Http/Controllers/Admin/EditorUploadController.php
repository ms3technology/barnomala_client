<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Validated image uploads for the Lexical editor.
 *
 * The client never trusts the user-provided filename: we generate a random
 * one on the server, validate MIME + size, store via Laravel's filesystem,
 * and return only the public URL plus the dimensions.
 */
class EditorUploadController extends Controller
{
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'max:5120'],
        ], [
            'image.required' => 'Please choose an image to upload.',
            'image.mimetypes' => 'Only JPEG, PNG, WebP, or GIF images are allowed.',
            'image.max' => 'The image must be 5 MB or smaller.',
        ]);

        $file = $request->file('image');
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'Upload failed.'], 422);
        }

        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';
        $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'bin');
        $filename = Str::random(20) . '.' . $extension;

        $directory = 'editor/' . date('Y/m');
        $path = $file->storeAs($directory, $filename, 'public');

        if (!$path) {
            return response()->json(['message' => 'Could not store the upload.'], 500);
        }

        $url = Storage::disk('public')->url($path);
        $absolutePath = Storage::disk('public')->path($path);

        [$width, $height] = $this->probeDimensions($absolutePath);

        return response()->json([
            'url' => $url,
            'path' => $path,
            'alt' => $file->getClientOriginalName(),
            'width' => $width,
            'height' => $height,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);
    }

    /**
     * Read image dimensions without pulling in an image library.
     * Returns `[0, 0]` on failure so the response shape stays consistent.
     *
     * @return array{0:int,1:int}
     */
    private function probeDimensions(string $absolutePath): array
    {
        if (!is_file($absolutePath)) {
            return [0, 0];
        }
        $info = @getimagesize($absolutePath);
        if (!is_array($info) || count($info) < 3) {
            return [0, 0];
        }
        return [(int) $info[0], (int) $info[1]];
    }
}
