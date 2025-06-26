<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     */
    public function index()
    {
        // Ultimi 10 articoli pubblicati, ordinati dal più recente
        $articles = Article::with(['user', 'tags'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('home', compact('articles'));
    }
}
