<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre_usuario',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'usuario_generos', 'usuario_id', 'genero_id');
    }

    public function historialPasswords()
    {
        return $this->hasMany(HistorialPassword::class, 'usuario_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'usuario_id');
    }

    public function busquedas()
    {
        return $this->hasMany(Busqueda::class, 'usuario_id');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'usuario_id');
    }
}
