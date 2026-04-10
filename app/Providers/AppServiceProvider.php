<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Password::defaults(function () {
            return Password::min(8)
                           ->letters()
                           ->mixedCase() 
                           ->numbers()
                           ->symbols()
                           ->uncompromised();
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('¡Bienvenido a HamfuHuddle! Verifica tu cuenta 🐹')
                ->view('emails.verify-mail', [
                    'url' => $url,
                    'notifiable' => $notifiable
                ]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Restablecer tu contraseña - HamfuHuddle 🔑')
                ->view('emails.reset-password', [
                    'url' => $url,
                    'notifiable' => $notifiable
                ]);
        });
    }
}
