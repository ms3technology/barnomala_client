<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // Using GD driver as default for Intervention Image v3
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Convert an image to WebP and store it.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @param int $quality
     * @return string The path to the stored WebP image
     */
    public function convertToWebp(UploadedFile $file, string $directory, string $disk = 'public', int $quality = 80): string
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $webpFilename = $filename . '_' . time() . '.webp';
        $path = trim($directory, '/') . '/' . $webpFilename;

        $image = $this->manager->read($file->getRealPath());
        $encoded = $image->toWebp($quality);

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }
}
