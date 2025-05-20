<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(10);
        return view('news.index', compact('news'));
    }

    public function create()
    {
        return view('news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 's3');
            Storage::disk('s3')->setVisibility($imagePath, 'public');
        }

        News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'excerpt' => Str::limit(strip_tags($request->content), 150),
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Notícia criada com sucesso!');
    }

    public function edit(News $news)
    {
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // Deleta imagem anterior (opcional)
            if ($news->image) {
                Storage::disk('s3')->delete($news->image);
            }

            $imagePath = $request->file('image')->store('news', 's3');
            Storage::disk('s3')->setVisibility($imagePath, 'public');
            $news->image = $imagePath;
        }

        $news->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'excerpt' => Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'image' => $news->image, // mantém a anterior se nenhuma nova for enviada
        ]);

        return redirect()->route('admin.news.index')->with('success', 'Notícia atualizada com sucesso!');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('s3')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Notícia excluída com sucesso!');
    }
}
