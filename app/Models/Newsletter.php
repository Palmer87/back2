<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Newsletter",
    title: "Newsletter",
    description: "Objet représentant une newsletter (archive ou projet)",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "sujet", type: "string", example: "Nouveau communiqué"),
        new OA\Property(property: "contenu", type: "string", example: "Contenu du communiqué..."),
        new OA\Property(property: "statut", type: "string", enum: ["brouillon", "envoyé"], example: "brouillon"),
        new OA\Property(property: "date_programmee", type: "string", format: "date-time", example: "2026-03-25 10:00:00"),
        new OA\Property(property: "date_envoi", type: "string", format: "date-time", example: "2026-03-19 15:30:00"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Newsletter extends Model
{
    protected $fillable = [
        'sujet',
        'contenu',
        'statut',
        'date_programmee',
        'date_envoi',
    ];

    protected $casts = [
        'date_programmee' => 'datetime',
        'date_envoi' => 'datetime',
    ];
}
