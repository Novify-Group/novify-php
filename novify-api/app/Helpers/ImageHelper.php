<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Save a base64 image to storage and return the path
     *
     * @param string $base64Image
     * @param string $folder
     * @return string
     */
    public static function saveBase64Image(?string $base64Image, string $folder): ?string
    {
        if (!$base64Image) {
            return null;
        }

        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $filename = Str::uuid() . '.jpg';
        $path = $folder . '/' . $filename;
        
        Storage::disk('public')->put($path, $image);
        
        return $path;
    }
} 