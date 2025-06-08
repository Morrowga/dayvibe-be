<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(Media $media)
    {
        $disk = Storage::disk('s3');

        if (! $disk->exists($media->getPath('webp'))) {
            abort(404, 'File not found');
        }

        $stream = $disk->readStream($media->getPath('webp'));

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $media->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
