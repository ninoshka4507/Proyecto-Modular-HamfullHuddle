<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Busqueda extends Model
{

    protected $table = 'busquedas';

    protected $fillable = [
        'usuario_id',
        'tmdb_id',
        'tipo'
    ];

    public $timestamps = false;

}