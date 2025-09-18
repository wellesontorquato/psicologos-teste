<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileProxyController extends Controller
{
    public function public(string $path, Request $request)
    {
        $disk = Storage::disk('s3');

        // (Opcional) Whitelist de pastas públicas
        // Ex.: só permitir news/ e profile-photos/
        if (!preg_match('#^(news|profile-photos)/#', $path)) {
            abort(404);
        }

        if (!$disk->exists($path)) {
            abort(404);
        }

        $mime        = $disk->mimeType($path) ?: 'application/octet-stream';
        $size        = $disk->size($path);
        $lastModTime = $disk->lastModified($path);

        // ETag/If-None-Match (cache do browser)
        $etag = sprintf('W/"%s-%s"', $size, $lastModTime);
        if ($request->headers->get('if-none-match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }

        // Last-Modified/If-Modified-Since (cache do browser)
        $lastModHttp = gmdate('D, d M Y H:i:s', $lastModTime) . ' GMT';
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
            'Content-Type'        => $mime,
            'Content-Length'      => $size,
            'Cache-Control'       => 'public, max-age=31536000, immutable',
            'ETag'                => $etag,
            'Last-Modified'       => $lastModHttp,
            // Se servir fontes/JS/CSS de outros domínios, libere CORS se quiser:
            // 'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
