<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HistorialPassword extends Model
{
    protected $fillable = ['usuario_id', 'password'];
}