<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // ✅ Limites editoriais (banco suporta 255, mas vamos limitar por UX/SEO)
    private const TITLE_MAX = 120;
    private const SUBTITLE_MAX = 200;
    private const EXCERPT_MAX = 150;

    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $news = $query->latest()->paginate(10);

        return view('news.index', compact('news'));
    }

    public function create()
    {
        return view('news.create', [
            'TITLE_MAX' => self::TITLE_MAX,
            'SUBTITLE_MAX' => self::SUBTITLE_MAX,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:' . self::TITLE_MAX,
            'subtitle' => 'nullable|string|max:' . self::SUBTITLE_MAX,
            'category' => 'required|string|max:255',
            'content'  => 'required|string',
            'image'    => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $safe = Str::slug($base, '-') ?: 'image';

            // ✅ nome único e "limpo"
            $name = Str::uuid()->toString() . '_' . $safe . '.' . $ext;

            $imagePath = Storage::disk('s3')->putFileAs(
                'news',
                $file,
                $name,
                [
                    'visibility'   => 'public',
                    'CacheControl' => 'public, max-age=31536000, immutable',
                    'ContentType'  => $file->getMimeType(),
                ]
            );
        }

        // ✅ slug único e estável
        $slug = Str::slug($request->title) . '-' . Str::lower(Str::random(8));

        News::create([
            'title'    => $request->title,
            'slug'     => $slug,
            'subtitle' => $request->subtitle,
            'category' => $request->category,
            'excerpt'  => Str::limit(trim(strip_tags($request->content)), self::EXCERPT_MAX),
            'content'  => $request->content,
            'image'    => $imagePath, // caminho relativo no S3
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Notícia criada com sucesso!');
    }

    public function edit(News $news)
    {
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:' . self::TITLE_MAX,
            'subtitle' => 'nullable|string|max:' . self::SUBTITLE_MAX,
            'category' => 'required|string|max:255',
            'content'  => 'required|string',
            'image'    => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120',
        ]);

        // ✅ Se o título mudou, aí sim atualiza slug (senão mantém)
        $titleChanged = $validated['title'] !== $news->title;

        // upload de nova imagem se houver
        if ($request->hasFile('image')) {
            try {
                if ($news->image && Storage::disk('s3')->exists($news->image)) {
                    Storage::disk('s3')->delete($news->image);
                }
            } catch (\Throwable $e) {
                // opcional: log
            }

            $file = $request->file('image');

            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $safe = Str::slug($base, '-') ?: 'image';
            $name = Str::uuid()->toString() . '_' . $safe . '.' . $ext;

            $news->image = Storage::disk('s3')->putFileAs(
                'news',
                $file,
                $name,
                [
                    'visibility'   => 'public',
                    'CacheControl' => 'public, max-age=31536000, immutable',
                    'ContentType'  => $file->getMimeType(),
                ]
            );
        }

        $updateData = [
            'title'    => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'category' => $validated['category'],
            'excerpt'  => Str::limit(trim(strip_tags($validated['content'])), self::EXCERPT_MAX),
            'content'  => $validated['content'],
            'image'    => $news->image,
        ];

        if ($titleChanged) {
            $updateData['slug'] = Str::slug($validated['title']) . '-' . Str::lower(Str::random(8));
        }

        $news->update($updateData);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Notícia atualizada com sucesso!');
    }

    public function destroy(News $news)
    {
        try {
            if ($news->image && Storage::disk('s3')->exists($news->image)) {
                Storage::disk('s3')->delete($news->image);
            }
        } catch (\Throwable $e) {
            // opcional: log
        }

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Notícia excluída com sucesso!');
    }
}
