<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{

    protected $table = 'recomendaciones';

    protected $fillable = [
        'usuario_id',
        'busqueda_id',
        'tmdb_id',
        'posicion',
        'origen',
        'score'
    ];

    public $timestamps = false;

}