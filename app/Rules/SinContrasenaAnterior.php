<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SinContrasenaAnterior implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = request('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            if (Hash::check($value, $user->password)) {
                $fail('La nueva contraseña no puede ser igual a tu contraseña actual.');
                return;
            }

            foreach ($user->historialPasswords as $historial) {
                if (Hash::check($value, $historial->password)) {
                    $fail('Ya has utilizado esta contraseña en el pasado. Por seguridad, elige una totalmente nueva.');
                    return;
                }
            }
        }
    }
}