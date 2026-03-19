<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
