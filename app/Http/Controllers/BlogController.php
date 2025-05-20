<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class BlogController extends Controller
{
    /**
     * Exibe a lista de notícias publicadas no blog (/blog)
     */
    public function index()
    {
        $news = News::latest()->paginate(6); // paginação simples
        return view('pages.blog', compact('news'));
    }

    /**
     * Exibe uma notícia individual pelo slug (/blog/{slug})
     */
    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        return view('news.show', compact('news'));
    }
}
