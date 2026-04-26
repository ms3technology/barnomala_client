<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'file|max:10240',
            'folder' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $folder = $request->input('folder', 'uploads');
        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            try {
                if ($file->isValid()) {
                    $path = $file->store($folder, 'public');
                    $uploadedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'url' => Storage::url($path),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ];
                } else {
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => 'Invalid file'
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $uploadedFiles,
            'errors' => $errors,
            'summary' => [
                'uploaded' => count($uploadedFiles),
                'failed' => count($errors)
            ]
        ]);
    }
}
