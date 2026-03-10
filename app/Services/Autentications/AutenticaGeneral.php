<?php

namespace App\Services\Autentications;

use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Subsi54;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\Generales;
use App\Services\Utils\SenderEmail;

class AutenticaGeneral
{
    protected $caja;

    protected $message;

    protected $afiliado;

    protected $procesadorComando;

    protected $tipo;

    protected $tipoName;

    protected $estadoAfiliado;

    public function __construct()
    {
        $this->procesadorComando = new ApiSubsidio();
        $this->caja = Mercurio02::first();
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getAfiliado()
    {
        return $this->afiliado;
    }

    public function generaCode()
    {
        return genera_code();
    }

    /**
     * prepareMail function
     *
     * @param  Mercurio07  $usuario
     * @param  string  $clave
     * @param  string  $url_activa
     * @param  string  $tipo_afiliado
     * @return void
     */
    public function prepareMail($usuario, $clave, $tipoName = null)
    {
        $tipoName = (is_null($tipoName)) ? $this->tipoName : $tipoName;
        $coddoc_detalle = Generales::TipoDocumento($usuario);
        $nombre = capitalize($usuario->getNombre());
        $asunto = 'Asignación de clave sistema Comfaca En Línea';
        $msj = "Bienvenido a {$this->caja->getRazsoc()}, a continuación confirmamos sus datos de usuario para el ingreso a nuestro portal web. " .
            "Y continuar el proceso de solicitud de afiliación.<br/>
        Credenciales de acceso:<br/><br/>
        TIPO AFILIADO: {$tipoName}<br/>
        TIPO DOCUMENTO: {$coddoc_detalle}<br/>
        USUARIO: {$usuario->getDocumento()}<br/>
        CLAVE: {$clave}<br/>";

        $html = view('emails.send-credentials', [
            'titulo' => "Cordial saludo,<br/>Señor@ {$nombre}",
            'msj' => $msj,
            'url_activa' => env('APP_URL') . '/Mercurio/Mercurio/login/index',
            'fecha' => date('Y-m-d'),
            'nombre' => $nombre,
            'razon' => $nombre,
            'tipo' => $tipoName,
            'asunto' => $asunto,
        ])->render();

        $emailCaja = (new Mercurio01)->findFirst();

        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            [[
                'email' => $usuario->getEmail(),
                'nombre' => $nombre,
            ]],
            $html
        );
    }

    /**
     * cambiarClave function
     *
     * @return bool
     */
    public function cambiarClave()
    {
        if (is_null($this->afiliado)) {
            return false;
        }

        $this->generaCode();

        $clave = genera_clave(8);
        $hash = clave_hash($clave);

        $this->afiliado->clave = $hash;
        $this->afiliado->save();

        $tipo_documento = Generales::TipoDocumento($this->afiliado);
        $nombre = capitalize($this->afiliado->nombre);
        $asunto = 'Recuperacion de clave - Comfaca En Linea';
        $msj = 'En respuesta a la solicitud de recuperación de cuenta, se realiza el cambio automatico de la clave para el inicio de sesión. ' .
            "A continuación enviamos las credenciales de acceso.<br/>
            Credenciales de acceso:<br/><br/>
            TIPO AFILIADO: {$this->tipoName}<br/>
            TIPO DOCUMENTO: {$tipo_documento}<br/>
            USUARIO: {$this->afiliado->documento}<br/>
            CLAVE: {$clave}<br/>";

        $html = view('emails.change-clave', [
            'titulo' => "Cordial saludo, señor@ {$nombre}",
            'msj' => $msj,
            'url_activa' => env('APP_URL') . '/web/login',
            'fecha' => date('Y-m-d'),
            'nombre' => $nombre,
            'razon' => $nombre,
            'tipo' => $this->tipoName,
            'asunto' => $asunto,

        ])->render();

        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->email}",
            "emisor_clave: {$emailCaja->clave}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            [[
                'email' => $this->afiliado->email,
                'nombre' => $nombre,
            ]],
            $html
        );

        return true;
    }

    public function verificaPin($usuario, $codigo_verify)
    {
        $date = new \DateTime('now');
        $nombre = capitalize($usuario->getNombre());
        $html = view(
            'emails.verify-pin',
            [
                'fecha' => date_format($date, 'd - M - Y'),
                'asunto' => 'Acceso a usuario, Comfaca En Linea',
                'tipo' => 'Usuario',
                'nombre' => $nombre,
                'razon' => $nombre,
                'msj' => "El usuario ha realizado el registro mediante validación de PIN, al portal web Comfaca En Línea.<br/>
            Utiliza el siguiente código de verificación, para confirmar el propietario de la dirección de correo:<br/>
            <span style=\"font-size:16px;color:#333\">CÓDIGO DE VERIFICACIÓN: </span>
            <span style=\"font-size:30px;color:#11cdef\"><b>{$codigo_verify}</b></span>",
            ]
        )->render();

        $asunto = 'Comprobación usuario portal Comfaca En Linea COMFACA';
        $emailCaja = (new Mercurio01)->findFirst();
        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            [[
                'email' => $usuario->getEmail(),
                'nombre' => $usuario->getNombre(),
            ]],
            $html
        );
    }

    public function getEstadoAfiliado()
    {
        return $this->estadoAfiliado;
    }


    /**
     * Obtiene los parámetros necesarios para la autenticación
     *
     * @return array
     */
    public function paramsAuthentication()
    {
        $tipsoc = [];
        $coddoc = [];
        $detadoc = [];
        $codciu = [];

        foreach (Subsi54::all() as $entity) {
            $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                continue;
            }
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') {
                continue;
            }
            $detadoc["{$entity->getCodrua()}"] = $entity->getDetdoc();
        }

        foreach (Gener09::where('codzon', '>=', 18000)->where('codzon', '<=', 19000)->get() as $entity) {
            $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
        }

        return [
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc,
        ];
    }
}
