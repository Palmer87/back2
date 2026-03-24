<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Project",
    title: "Project",
    description: "Modèle représentant un projet de l'organisation",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Nom du projet"),
        new OA\Property(property: "slug", type: "string", example: "nom-du-projet"),
        new OA\Property(property: "description", type: "string", example: "Description détaillée du projet..."),
        new OA\Property(property: "status", type: "string", example: "en cours"),
        new OA\Property(property: "image_path", type: "string", nullable: true, example: "projects/image.png"),
        new OA\Property(property: "full_image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/projects/image.png"),
        new OA\Property(property: "start_date", type: "string", format: "date", nullable: true),
        new OA\Property(property: "end_date", type: "string", format: "date", nullable: true),
        new OA\Property(property: "auteur", type: "integer", description: "ID de l'utilisateur ayant créé le projet", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'image_path',
        'images',
        'start_date',
        'end_date',
        'auteur',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected $appends = ['full_image_url', 'all_images_urls'];

    public function getFullImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getAllImagesUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }

        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->images);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'auteur');
    }
}
