<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageUploadTrait
{
    public function uploadImage($file, string $fileName, string $diskName, string $folder = 'uploads')
    {
        $path = Storage::disk($diskName)->putFileAs($folder, $file, $fileName, 'public');
        return $path;
    }

    public function getImageUrl(string $path, string $diskName)
    {
        return Storage::disk($diskName)->url($path);
    }

    public function removeImage(string $path, string $diskName)
    {
        Storage::disk($diskName)->delete($path);
    }
}
