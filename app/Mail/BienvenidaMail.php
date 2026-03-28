<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;
use App\Models\Organizacion;

class BienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $organizacion;

    /**
     * Create a new message instance.
     */
    public function __construct(Usuario $usuario, Organizacion $organizacion)
    {
        $this->usuario = $usuario;
        $this->organizacion = $organizacion;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('¡Bienvenido a Sistema APR! - Tu cuenta está lista')
                    ->view('emails.bienvenida')
                    ->with([
                        'usuario' => $this->usuario,
                        'organizacion' => $this->organizacion,
                    ]);
    }
}
