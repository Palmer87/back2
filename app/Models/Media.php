<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Media",
    title: "Media",
    description: "Modèle représentant un fichier média (image, vidéo, document)",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "image.jpg"),
        new OA\Property(property: "path", type: "string", example: "media/image.jpg"),
        new OA\Property(property: "url", type: "string", example: "http://localhost:8000/storage/media/image.jpg"),
        new OA\Property(property: "size", type: "integer", example: 1024),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Media extends Model
{
    protected $fillable = [
        'name',
        'path',
        'size',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}
