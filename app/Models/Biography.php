<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Biography",
    title: "Biography",
    description: "Modèle représentant la biographie d'une personnalité",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nom", type: "string", example: "Doe"),
        new OA\Property(property: "prenom", type: "string", example: "John"),
        new OA\Property(property: "date_naissance", type: "string", format: "date", nullable: true),
        new OA\Property(property: "lieu_naissance", type: "string", nullable: true),
        new OA\Property(property: "nationalite", type: "string", nullable: true),
        new OA\Property(property: "parcours_scolaire", type: "string", nullable: true),
        new OA\Property(property: "parcours_professionnel", type: "string", nullable: true),
        new OA\Property(property: "parcours_politique", type: "string", nullable: true),
        new OA\Property(property: "photo", type: "string", nullable: true, example: "biographies/photo.jpg"),
        new OA\Property(property: "auteur", type: "integer", description: "ID de l'administrateur", example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Biography extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'parcours_scolaire',
        'parcours_professionnel',
        'parcours_politique',
        'photo',
        'auteur',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'auteur');
    }
}
