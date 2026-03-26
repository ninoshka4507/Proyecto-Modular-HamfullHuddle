<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{

    public function showSelector()
    {
        $generos = Genero::all(); 
        $pageTitle = !session('genres_selected') ? 'Bienvenido' : 'Mis Géneros';
        $seleccionados = Auth::user()
        ->generos()
        ->pluck('generos.id')
        ->toArray();

       return view('registro.select_genres', compact('generos', 'pageTitle', 'seleccionados'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:generos,id', 
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        $user->generos()->sync($request->genres);

        session(['genres_selected' => true]);

        return redirect()->route('home');
    }
}