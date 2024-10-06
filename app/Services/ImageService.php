<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Handles the file upload and stores the image or media.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string|null
     */
    public static function storeFile(UploadedFile $file, string $directory): ?string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @return bool
     */
    public static function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }
}
