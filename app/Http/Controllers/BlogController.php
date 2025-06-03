<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class BlogController extends Controller
{
    /**
     * Exibe a lista de notícias publicadas no blog (/blog)
     */
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        $news = $query->latest()->paginate(6);

        return view('pages.blog', compact('news'));
    }


    /**
     * Exibe uma notícia individual pelo slug (/blog/{slug})
     */
    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        $related = News::where('id', '!=', $news->id)
                    ->where('category', $news->category)
                    ->latest()
                    ->take(3)
                    ->get();

        return view('news.show', compact('news', 'related'));
    }

    public function apiIndex()
    {
        $news = News::latest()->take(10)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt,
                'content' => $item->content,
                'image_url' => $item->image_url,
                'created_at' => $item->created_at->toDateTimeString(),
            ];
        });

        return response()->json($news);
    }
}