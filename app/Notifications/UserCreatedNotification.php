<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class UserCreatedNotification extends Notification
{
    protected $name;
    protected $email;

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cuenta Creada Exitosamente')
            ->greeting('Hola, ' . $this->name)
            ->line('Tu cuenta ha sido creada exitosamente. Ahora puedes ingresar con los siguientes datos:')
            ->line('Correo electrónico: ' . $this->email)
            ->line('Contraseña: ' . $this->name)
            ->action('Iniciar Sesión', url('/login'))  // Cambia la URL al enlace de login en tu aplicación
            ->line('Gracias por unirte a nuestra plataforma.');
    }
}
