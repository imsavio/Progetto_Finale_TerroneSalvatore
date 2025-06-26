<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function __construct()
    {
        // Middleware: solo utenti autenticati possono accedere a create, store, edit, update, destroy
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['user', 'tags'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::all();
        return view('articles.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        $validated['user_id'] = Auth::id();

        // Gestione upload immagine
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($validated);

        // Associa i tag
        if (isset($validated['tags'])) {
            $article->tags()->attach($validated['tags']);
        }

        return redirect()->route('articles.index')->with('success', 'Articolo creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        if (!$article->is_published && (!Auth::check() || Auth::id() !== $article->user_id)) {
            abort(404);
        }

        $article->load(['user', 'tags']);
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        // Solo l'autore può modificare l'articolo
        if (Auth::id() !== $article->user_id) {
            abort(403);
        }

        $tags = Tag::all();
        return view('articles.edit', compact('article', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        // Solo l'autore può modificare l'articolo
        if (Auth::id() !== $article->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        // Gestione upload immagine
        if ($request->hasFile('image')) {
            // Elimina la vecchia immagine se esiste
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($validated);

        // Sincronizza i tag
        if (isset($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        } else {
            $article->tags()->detach();
        }

        return redirect()->route('articles.show', $article)->with('success', 'Articolo aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Solo l'autore può eliminare l'articolo
        if (Auth::id() !== $article->user_id) {
            abort(403);
        }

        // Elimina l'immagine se esiste
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Articolo eliminato con successo!');
    }
}