<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'image_path',
        'start_date',
        'end_date',
        'auteur',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'auteur');
    }
}
