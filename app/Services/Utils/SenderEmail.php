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
        // Configuración del servidor SMTP (por defecto Gmail)
        $this->mail->isSMTP();
        $this->mail->Host = env('EMAIL_HOST', 'smtp.gmail.com');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('EMAIL_ACCOUNT', $this->emisor_email);
        $this->mail->Password = env('EMAIL_KEY', $this->emisor_clave);

        // Encripción y puerto configurables: tls->587, ssl->465
        $encryption = strtolower(env('EMAIL_ENCRYPTION', 'tls'));
        if (in_array($encryption, ['ssl', 'smtps'])) {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL implícito
            $this->mail->Port = (int) env('EMAIL_PORT', 465);
        } else {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS
            $this->mail->Port = (int) env('EMAIL_PORT', 587);
        }

        // Opcionales para entornos restrictivos
        $this->mail->SMTPAutoTLS = true;
        $this->mail->Timeout = (int) env('EMAIL_TIMEOUT', 15);
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
