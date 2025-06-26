<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user profile with their articles.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Articoli dell'utente corrente
        $articles = $user->articles()
            ->with('tags')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.index', compact('user', 'articles'));
    }
}