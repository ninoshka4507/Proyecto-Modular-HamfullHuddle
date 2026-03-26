<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $generosSeleccionados = $user->generos; 
        
        $pageTitle = 'Mi Perfil';

        return view('account.index', compact('user', 'generosSeleccionados', 'pageTitle'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nombre_visible' => 'nullable|string|max:100',
            'avatar' => 'nullable|string|max:100'
        ]);

        $user->nombre_visible = $request->nombre_visible;
        $user->avatar = $request->avatar;
        
        $user->save();

        return redirect()->route('account.index')->with('success', '¡Tu perfil ha sido actualizado con éxito!');
    }
}