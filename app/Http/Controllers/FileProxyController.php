<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileProxyController extends Controller
{
    /**
     * Proxy público de arquivos do bucket (Contabo via disk s3).
     * Ex.: https://psigestor.com/cdn/news/arquivo.webp
     */
    public function servePublic(string $path, Request $request)
    {
        $disk = Storage::disk('s3');

        // ✅ whitelist de pastas (ajuste conforme necessidade)
        if (!preg_match('#^(news|profile-photos|arquivos)/#', $path)) {
            abort(404, 'Path not allowed');
        }

        // Tenta abrir stream diretamente
        $stream = $disk->readStream($path);
        if (!$stream) {
            abort(404, 'File not found in storage');
        }

        // Metadados seguros
        $mime        = $disk->mimeType($path) ?: 'application/octet-stream';
        $size        = $disk->size($path);
        $lastModTime = $disk->lastModified($path);
        $etag        = sprintf('W/"%s-%s"', $size, $lastModTime);
        $lastModHttp = gmdate('D, d M Y H:i:s', $lastModTime) . ' GMT';

        // ✅ Respeita cache do navegador
        if ($request->headers->get('if-none-match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }
        if ($ims = $request->headers->get('if-modified-since')) {
            if (strtotime($ims) >= $lastModTime) {
                return response('', 304, ['ETag' => $etag, 'Last-Modified' => $lastModHttp]);
            }
        }

        // ✅ Resposta em stream (não carrega tudo em memória)
        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'   => $mime,
            'Content-Length' => $size,
            'Cache-Control'  => 'public, max-age=31536000, immutable',
            'ETag'           => $etag,
            'Last-Modified'  => $lastModHttp,
        ]);
    }
}
