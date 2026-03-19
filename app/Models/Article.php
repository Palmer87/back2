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
        'slug',
        'image_url',
        'video_url',
        'typePart',
        'auteur',
        'publier',
        'publier_le',
        'retirer_le',
    ];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur');
    }
}
