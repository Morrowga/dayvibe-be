<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function show(Media $media)
    {
        abort_unless(auth()->check(), 403);
        return redirect($media->getUrl());
    }
}
