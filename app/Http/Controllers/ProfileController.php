<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios,email', 'confirmed'],
        ]);

        $user = $request->user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();
        $user->sendEmailVerificationNotification();
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Correo actualizado. Te hemos enviado un enlace de verificación, por favor revisa tu bandeja de entrada e inicia sesión.');
    }

    public function resetData(Request $request)
    {
        $user = $request->user();
        $user->generos()->detach(); 
        $user->calificaciones()->delete(); 
        DB::table('vistas')->where('usuario_id', $user->id)->delete();
        $user->busquedas()->delete();         
        $user->recomendaciones()->delete();   
        
        return redirect()->route('genres.select')->with('success', 'Cuenta reiniciada con éxito. Por favor, selecciona tus géneros nuevamente.');
    }
}
