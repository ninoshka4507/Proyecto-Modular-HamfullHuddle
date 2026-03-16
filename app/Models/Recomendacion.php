<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{

    protected $table = 'recomendaciones';

    protected $fillable = [
        'busqueda_id',
        'tmdb_id',
        'posicion'
    ];

    public $timestamps = false;

}