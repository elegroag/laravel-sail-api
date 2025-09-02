<?php
namespace App\Services\Utils;

use App\Exceptions\DebugException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class SenderEmail 
{
    protected $email_pruebas = "soportesistemas.comfaca@gmail.com";
    protected $emisor_email;
    protected $emisor_nombre;
    protected $emisor_clave;
    protected $asunto;
    protected $mail;

    private function configureSMTP()
    {
        // Configuración del servidor SMTP de Gmail
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->emisor_email; // ¡Cambia esto por tu correo de Gmail!
        $this->mail->Password = $this->emisor_clave; // ¡Cambia esto por tu contraseña de aplicación!
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = 465;
        $this->mail->CharSet = 'UTF-8';
    }

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    public function setters(...$params)
    {
        $arguments = get_params_destructures($params);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
        return $this;
    }

    public function send(
        string|array $to = '',
        string $body = '',
        array|null $attachments = null,
        string $altBody = '',
        array|null $cc = null,
        array|null $bcc = null
    )
    {
        try {
            // Remitente
            $this->mail->setFrom($this->emisor_email, $this->emisor_nombre);

            // Destinatarios
            if (is_array($to)) {
                foreach ($to as $address) {
                    $this->mail->addAddress($address);
                }
            } else {
                $this->mail->addAddress($to);
            }

            // Copia (CC)
            if (is_array($cc)) {
                foreach ($cc as $address) {
                    $this->mail->addCC($address);
                }
            }

            // Copia oculta (BCC)
            if (is_array($bcc)) {
                foreach ($bcc as $address) {
                    $this->mail->addBCC($address);
                }
            }

            // Contenido
            $this->mail->isHTML(true); // Habilitar formato HTML
            $this->mail->Subject = $this->asunto;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody;

            // Archivos adjuntos
            if (is_array($attachments)) {
                foreach ($attachments as $attachmentPath) {
                    if (file_exists($attachmentPath)) {
                        $this->mail->addAttachment($attachmentPath);
                    } else {
                        throw new PHPMailerException("El archivo adjunto no existe: {$attachmentPath}");
                    }
                }
            }

            $this->mail->send();
            return 'Correo enviado exitosamente';
        } catch (PHPMailerException $e) {
            throw new DebugException("Error al enviar el correo: {$this->mail->ErrorInfo}");
        }
    }
}
