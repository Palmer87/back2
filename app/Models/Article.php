<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Article",
    title: "Article",
    description: "Modèle représentant un article de blog ou une actualité",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Titre de l'article"),
        new OA\Property(property: "content", type: "string", example: "Contenu de l'article..."),
        new OA\Property(property: "slug", type: "string", example: "titre-de-l-article"),
        new OA\Property(property: "image_url", type: "string", nullable: true, example: "images/photo.jpg"),
        new OA\Property(property: "video_url", type: "string", nullable: true, example: "videos/clip.mp4"),
        new OA\Property(property: "full_image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/images/photo.jpg"),
        new OA\Property(property: "full_video_url", type: "string", nullable: true, example: "http://localhost:8000/storage/videos/clip.mp4"),
        new OA\Property(property: "typePart", type: "string", example: "communique"),
        new OA\Property(property: "auteur", type: "integer", description: "ID de l'auteur", example: 1),
        new OA\Property(property: "publier_le", type: "string", format: "date", nullable: true),
        new OA\Property(property: "retirer_le", type: "string", format: "date", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
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

    protected $appends = ['full_image_url', 'full_video_url'];

    public function getFullImageUrlAttribute()
    {
        return $this->image_url ? asset('storage/' . $this->image_url) : null;
    }

    public function getFullVideoUrlAttribute()
    {
        return $this->video_url ? asset('storage/' . $this->video_url) : null;
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur');
    }
}
