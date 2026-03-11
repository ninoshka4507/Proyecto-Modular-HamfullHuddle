<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    /**
     * Muestra la vista de selección de géneros
     */
    public function showSelector()
    {
        // Obtenemos los géneros de la base de datos
        $generos = Genero::all(); 
        return view('registro.select_genres', compact('generos'));
    }

    /**
     * Guarda la selección del usuario
     */
    public function save(Request $request)
    {
        $request->validate([
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:generos,id', // Validación de seguridad
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Sincroniza los IDs (borra los viejos y pone los nuevos en la tabla pivot)
        $user->generos()->sync($request->genres);

        // Activamos la sesión para saltar el middleware
        session(['genres_selected' => true]);

        return redirect()->route('home');
    }
}