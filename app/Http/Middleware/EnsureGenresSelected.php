<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGenresSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->generos()->count() === 0) {
            $allowedRoutes = ['genres.select', 'genres.save', 'logout', 'profile.edit', 'account.index', 'password.update', 'profile.update_email', 'profile.reset_data', 'profile.destroy','verification.notice', 'verification.send', 'verification.verify'];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('genres.select')->with('error', 'Debes elegir al menos un género favorito para continuar.');
            }
        }

        return $next($request);
    }
}