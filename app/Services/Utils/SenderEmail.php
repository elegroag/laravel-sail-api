<?php
namespace App\Services\Utils;
require_once base_path('legacy/swiftmailer/lib/swift_required.php');

class SenderEmail 
{
    
    use Swift_Mailer;
    use Swift_Message;
    use Swift_SmtpTransport;
    private $email_pruebas = "soportesistemas.comfaca@gmail.com";
    private $emisor_email;
    private $emisor_clave;
    protected $asunto;

    public function __construct()
    {
        Core::importFromLibrary("Swift", "Swift.php");
        Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
    }

    public function setters(...$params)
    {
        $arguments = $this->getParams($params);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
        return $this;
    }

    public function send($destinatarios, $mensaje, $files = '')
    {
        $smtp = new Swift_Connection_SMTP(
            "smtp.gmail.com",
            Swift_Connection_SMTP::PORT_SECURE,
            Swift_Connection_SMTP::ENC_TLS
        );
        $smtp->setUsername($this->emisor_email);
        $smtp->setPassword($this->emisor_clave);

        $smsj = new Swift_Message();
        $swift = new Swift($smtp);
        $email = new Swift_RecipientList();

        $smsj->setCharset("UTF-8");
        $smsj->setContentType("text/html");
        $smsj->setSubject($this->asunto);

        $parte = new Swift_Message_Part($mensaje);
        $parte->setCharset('utf-8');
        $parte->setContentType("text/html");
        $smsj->attach($parte);

        foreach ($destinatarios as $ai => $destinatario) {
            if ($this->enviroment != "production") {
                $destinatario['email'] = $this->email_pruebas;
            }
            $email->addTo($destinatario['email'], $destinatario['nombre']);
        }

        if (!is_null($files) && $files != '') {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $type = substr(strrchr($file, "."), 1);
                    $mimeType = mimeType($type);
                    $swiftfile = new Swift_File($file);
                    $attachment = new Swift_Message_Attachment($swiftfile, basename($file), $mimeType);
                    $smsj->attach($attachment);
                }
            } else {
                $type = substr(strrchr($files, "."), 1);
                $mimeType = mimeType($type);
                $swiftfile = new Swift_File($files);
                $attachment = new Swift_Message_Attachment($swiftfile, basename($files), $mimeType);
                $smsj->attach($attachment);
            }
        }

        $swift->send($smsj, $email, new Swift_Address($this->emisor_email));
    }

    private function getParams($data)
    {
        $params = [];
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $item) {
                if (stristr($item, ':') === FALSE) {
                    $params[0] = $item;
                    continue;
                }
                $name = substr($item, 0, strpos($item, ':'));
                $params[$name] = substr($item, strpos($item, ':') + 1);
            }
        }
        return $params;
    }
}
