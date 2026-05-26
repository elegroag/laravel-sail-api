<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombre;
    public string $emailRemitente;
    public string $asunto;
    public string $mensaje;
    public string $fecha;

    public function __construct(string $nombre, string $emailRemitente, string $asunto, string $mensaje)
    {
        $this->nombre = $nombre;
        $this->emailRemitente = $emailRemitente;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->fecha = now()->format('d/m/Y H:i');
    }

    public function build(): Mailable
    {
        return $this
            ->to('afiliacionyregistro@comfaca.com')
            ->replyTo($this->emailRemitente)
            ->subject("Nuevo mensaje de contacto: {$this->asunto}")
            ->view('emails.contact-form')
            ->with([
                'nombre' => $this->nombre,
                'emailRemitente' => $this->emailRemitente,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
                'fecha' => $this->fecha,
            ]);
    }
}