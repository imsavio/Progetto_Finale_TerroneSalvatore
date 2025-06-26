<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
     use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'image',
        'is_published',
        'user_id'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Relazione One-to-Many con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relazione Many-to-Many con Tag
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
