<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Document",
    title: "Document",
    description: "Modèle représentant un document téléchargeable (PDF, etc.)",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Rapport Annuel 2023"),
        new OA\Property(property: "description", type: "string", example: "Description du rapport..."),
        new OA\Property(property: "file", type: "string", example: "documents/rapport.pdf"),
        new OA\Property(property: "type", type: "string", example: "PDF"),
        new OA\Property(property: "views", type: "integer", example: 120),
        new OA\Property(property: "downloads", type: "integer", example: 45),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Document extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file',
        'type',
        'views',
        'downloads',
    ];
}
