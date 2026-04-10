<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = 'generos';
    public $timestamps = false;
    protected $fillable = ['nombre'];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_generos', 'genero_id', 'usuario_id');
    }
}