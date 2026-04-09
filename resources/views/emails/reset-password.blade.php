<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 0; }
        .wrapper { width: 100%; background-color: #f4f4f5; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .banner { background-color: #1a103d; text-align: center; padding: 30px 20px; }
        .banner h1 { color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 2px; }
        .banner span { color: #9F7CFF; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .title { font-size: 22px; font-weight: bold; color: #1a103d; margin-bottom: 20px; }
        .button-container { text-align: center; margin: 35px 0; }
        .button { background-color: #9F7CFF; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block; }
        .footer { background-color: #fafafa; text-align: center; padding: 20px; font-size: 12px; color: #888888; border-top: 1px solid #eeeeee; }
        .link-text { color: #9F7CFF; word-break: break-all; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            
            <div class="banner">
                <h1><span>H</span>amfu<span>H</span>uddle</h1>
            </div>

            <div class="content">
                <div class="title">Hola, {{ $notifiable->nombre_usuario ?? 'Hamster' }}! 🐹</div>

                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>HamfuHuddle</strong>. Si fuiste tú, simplemente haz clic en el botón de abajo para elegir una nueva contraseña:</p>

                <div class="button-container">
                    <a href="{{ $url }}" class="button" style="color: #ffffff;">Restablecer mi contraseña</a>
                </div>

                <p style="font-size: 14px; color: #666; margin-top: 30px; border-left: 3px solid #9F7CFF; padding-left: 10px;">
                    🚨 <strong>¿No solicitaste este cambio?</strong> No te preocupes. Puedes ignorar este correo tranquilamente; tu contraseña actual seguirá siendo válida y tu cuenta está segura.
                </p>
                
                <p style="font-size: 12px; color: #888; margin-top: 20px;">
                    *Este enlace expirará en 60 minutos por razones de seguridad.
                </p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} HamfuHuddle. Todos los derechos reservados.<br><br>
                ¿Tienes problemas con el botón? Copia y pega este enlace en tu navegador:<br>
                <a href="{{ $url }}" class="link-text">{{ $url }}</a>
            </div>

        </div>
    </div>
</body>
</html>