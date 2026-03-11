<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Cargamos los géneros que el usuario ya tiene seleccionados
        $generosSeleccionados = $user->generos; 

        return view('account.index', compact('user', 'generosSeleccionados'));
    }
}