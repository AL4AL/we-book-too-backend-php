<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class ImageProxyController extends Controller
{
    public function serve(Request $request, $size)
    {
        // Validate size
        if (!in_array($size, ['base', 'sm', 'lg'])) {
            abort(404);
        }

        $referer = $request->headers->get('referer') ?? $request->headers->get('origin');
        if (!$referer) {
            return response('Referer header required', 400);
        }

        // TODO: Security validation for Referer header (see todo list)
        // Placeholder for future implementation

        // Parse domain from referer
        $host = parse_url($referer, PHP_URL_HOST);
        if (!$host) {
            return response('Invalid Referer header', 400);
        }

        $key = "images/{$size}/{$host}-home-page-background.webp";

        $disk = Storage::disk('s3');
//        if (!$disk->exists($key)) { // TODO: this code should exists but didn't work so I commented it out, fix it
//            abort(404);
//        }

        $stream = $disk->readStream($key);
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}