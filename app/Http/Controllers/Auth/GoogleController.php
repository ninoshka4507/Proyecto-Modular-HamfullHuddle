<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

public function handleGoogleCallback()
    {
        
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                    'nombre_usuario' => $googleUser->getName(),
                    'nombre_visible' => $googleUser->getName(), // Opcional: llenar este también
                    'email'          => $googleUser->getEmail(),
                    'avatar'         => $googleUser->getAvatar(),
                    'password'       => bcrypt(Str::random(16)), 
                ]);
                
            Auth::login($user);
            return redirect('/select_genres'); // Ajusta a tu ruta real
        }

        Auth::login($user);
        return redirect('/home');
    }
}