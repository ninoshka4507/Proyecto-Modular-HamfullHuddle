<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tmdb_id',
        'calificacion',
        'tipo'
    ];
}