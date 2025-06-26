<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color'
    ];

    // Relazione Many-to-Many con Article
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    // Genera automaticamente lo slug dal nome
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }
}