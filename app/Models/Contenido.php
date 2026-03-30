<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenido extends Model
{
    protected $table = 'contenido';

    protected $fillable = [
        'tmdb_id',
        'tipo',
        'titulo',
        'poster_path',
        'generos',
        'popularidad',
        'overview',
        'cast_list',
        'director',
        'rating',
        'votes',
        'year',
        'language'
    ];

    public $timestamps = false;
}