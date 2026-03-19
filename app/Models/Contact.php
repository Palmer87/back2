<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Contact",
    title: "Contact",
    description: "Modèle représentant un message de contact",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nom", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
        new OA\Property(property: "telephone", type: "string", nullable: true, example: "+2250102030405"),
        new OA\Property(property: "sujet", type: "string", example: "Demande de devis"),
        new OA\Property(property: "message", type: "string", example: "Bonjour, je souhaiterais..."),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Contact extends Model
{
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'sujet',
        'message',
    ];
}
