@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="bg-primary text-white p-5 rounded">
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-3">Benvenuto nel nostro Blog</h1>
                <p class="lead mb-4">Scopri gli ultimi articoli e condividi le tue idee con la nostra community</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">Registrati</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Accedi</a>
                @else
                    <a href="{{ route('articles.create') }}" class="btn btn-light btn-lg">Scrivi un articolo</a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Latest Articles Section -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Ultimi Articoli</h2>
            <a href="{{ route('articles.index') }}" class="btn btn-outline-primary">Vedi tutti</a>
        </div>
    </div>
</div>

@if($articles->count() > 0)
    <div class="row">
        @foreach($articles as $article)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($article->image)
                        <img src="{{ asset('storage/' . $article->image) }}" 
                             class="card-img-top" alt="{{ $article->title }}" 
                             style="height: 200px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="{{ route('articles.show', $article) }}" 
                               class="text-decoration-none text-dark">
                                {{ $article->title }}
                            </a>
                        </h5>
                        
                        @if($article->excerpt)
                            <p class="card-text text-muted">{{ Str::limit($article->excerpt, 100) }}</p>
                        @else
                            <p class="card-text text-muted">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                        @endif
                        
                        <div class="mt-auto">
                            <!-- Tags -->
                            @if($article->tags->count() > 0)
                                <div class="mb-2">
                                    @foreach($article->tags as $tag)
                                        <span class="badge rounded-pill" style="background-color: {{ $tag->color }};">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Author and Date -->
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    di {{ $article->user->name }}
                                </small>
                                <small class="text-muted">
                                    {{ $article->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <h3 class="text-muted">Nessun articolo pubblicato</h3>
                <p class="text-muted">Sii il primo a condividere un articolo!</p>
                @auth
                    <a href="{{ route('articles.create') }}" class="btn btn-primary">Scrivi il primo articolo</a>
                @endauth
            </div>
        </div>
    </div>
@endif
@endsection