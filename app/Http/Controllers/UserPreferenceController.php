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
        
        /** @var User $user */
        $user = Auth::user();
        $seleccionados = $user->generos()->pluck('generos.id')->toArray();
        $isEditing = count($seleccionados) > 0;
        $pageTitle = $isEditing ? 'Mis Géneros' : 'Bienvenido';

        return view('registro.select_genres', compact('generos', 'pageTitle', 'seleccionados', 'isEditing'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:generos,id', 
        ]);

        /** @var User $user */
        $user = Auth::user();
        $yaTeniaGeneros = $user->generos()->exists();
        $user->generos()->sync($request->genres);

        session()->forget('genres_selected');

        if (!$yaTeniaGeneros) {
            return redirect()->route('home');
        } else {
            return redirect()->route('account.index')->with('status', '¡Tus gustos han sido actualizados!');
        }
    }
}