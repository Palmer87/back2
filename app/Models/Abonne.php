<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Abonne",
    title: "Abonne",
    description: "Modèle représentant un abonné à la newsletter",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "email", type: "string", format: "email", example: "subscriber@example.com"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Abonne extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
    ];
}
