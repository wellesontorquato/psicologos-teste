<?php

// app/Http/Controllers/FileProxyController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileProxyController extends Controller
{
    public function servePublic(string $path, Request $request)
    {
        $disk = Storage::disk('s3');

        // (opcional) whitelist de pastas
        if (!preg_match('#^(news|profile-photos)/#', $path)) {
            abort(404);
        }

        if (!$disk->exists($path)) {
            abort(404);
        }

        $mime        = $disk->mimeType($path) ?: 'application/octet-stream';
        $size        = $disk->size($path);
        $lastModTime = $disk->lastModified($path);
        $etag        = sprintf('W/"%s-%s"', $size, $lastModTime);
        $lastModHttp = gmdate('D, d M Y H:i:s', $lastModTime) . ' GMT';

        if ($request->headers->get('if-none-match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }
        if ($ims = $request->headers->get('if-modified-since')) {
            if (strtotime($ims) >= $lastModTime) {
                return response('', 304, ['ETag' => $etag, 'Last-Modified' => $lastModHttp]);
            }
        }

        return new StreamedResponse(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'   => $mime,
            'Content-Length' => $size,
            'Cache-Control'  => 'public, max-age=31536000, immutable',
            'ETag'           => $etag,
            'Last-Modified'  => $lastModHttp,
        ]);
    }
}
