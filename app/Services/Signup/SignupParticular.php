<?php

namespace App\Services\Signup;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Services\Request;
use App\Services\Utils\CrearUsuario;
use App\Services\Utils\Generales;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;

class SignupParticular
{

    public $coddoc;
    public $documento;
    public $tipo;
    public $cedrep;
    public $tipdoc;
    public $repleg;
    public $email;
    public $codciu;
    public $tipper;
    public $telefono;
    public $calemp;
    public $tipsoc;
    public $coddocrepleg;
    public $razsoc;
    public $usuario;
    public $tipemp;
    public $nit;
    public $nombre;
    private $codigo_verify;
    private $password;

    public function __construct(Request| null $params = null)
    {
        if ($params instanceof Request) {
            foreach ($params->getKeys() as $key) if (property_exists($this, $key)) $this->$key = $params->getParam($key);
        }
    }

    /**
     * main function
     * @return SignupParticular
     */
    public function main()
    {
        $coddocReps = coddoc_repleg_array();
        if ($this->calemp == 'E') {
            $flip = array_flip($coddocReps);
            $codeDocumentoRep = $flip[$this->coddocrepleg];
        } else {
            //Personas naturales por defecto
            $codeDocumentoRep = $this->coddoc;
            $this->coddocrepleg = $coddocReps[$this->coddoc];
            $this->razsoc = $this->repleg;
        }

        $this->coddoc = ($this->tipper == 'J') ? $this->coddoc : $codeDocumentoRep;
        $this->documento = ($this->tipper == 'J') ? $this->nit : $this->cedrep;
        $this->nombre = ($this->tipper == 'J') ?  $this->razsoc : $this->repleg;
        $this->tipdoc = $codeDocumentoRep;
        $this->tipo = $this->tipo; // Usar el tipo real del request en lugar de hardcodear 'P'
        $this->createUserMercurio();
        return $this;
    }

    /**
     * createUserMercurio function
     * @return Mercurio07
     */
    public function createUserMercurio()
    {
        $this->generaCode();
        $usuarioParticular = Mercurio07::where(["tipo" => $this->tipo, "coddoc" => $this->coddoc, "documento" => $this->documento])->first();

        if ($usuarioParticular == false) {
            $hash = clave_hash($this->password);
            $crearUsuario = new CrearUsuario();
            $crearUsuario->setters(
                "tipo: {$this->tipo}",
                "coddoc: {$this->coddoc}",
                "documento: {$this->documento}",
                "nombre: {$this->nombre}",
                "email: {$this->email}",
                "codciu: {$this->codciu}",
                "clave: {$hash}"
            );
            $usuarioParticular = $crearUsuario->procesar();

            $crearUsuario->crearOpcionesRecuperacion($this->codigo_verify);
        } else {
            if ($usuarioParticular->getEstado() == "A") {
                throw new DebugException("El usuario ya existe y se encuentra registrado en el sistema. " .
                    "La solicitud para afiliación está pendiente de enviar, compruebe las credenciales de acceso en la dirección de correo registrada previamente: " .
                    mask_email($usuarioParticular->getEmail()) . ". <br/>" .
                    " Y ahora puedes ingresar por la opción \"2 Afiliación Pendiente\" continua el proceso de afiliación.", 501);
            }
            //actualiza y activa la cuenta de la persona solo si el correo es igual al reportado
        }
        $this->preparaMail($usuarioParticular, $this->password);

        return $usuarioParticular;
    }


    function preparaMail($usuario, $clave)
    {
        $coddoc_detalle = Generales::TipoDocumento($usuario);
        $url_activa = env("APP_URL");
        $date = Carbon::now();
        $html = view(
            "templates/tmp_register",
            array(
                "fecha" => $date->format("d - M - Y"),
                "asunto" => "Acceso a usuario, Comfaca En Linea",
                "tipo" => ($this->tipo == 'P') ? 'Usuario' : 'Usuario Empresa',
                "nombre" => $this->nombre,
                "razon" => $this->razsoc,
                "msj" => "El usuario ha realizado el registro al portal web Comfaca En Línea.
                Las siguientes son credeciales de acceso: <br>
                TIPO DOCUMENTO {$coddoc_detalle}<br/>
                DOCUMENTO {$this->documento}<br/>
                CLAVE {$clave}<br/><br/>
                Utiliza el siguiente código de verificación, para confirmar el propietario de la dirección de correo:<br/>
                <span style=\"font-size:16px;color:#333\">CÓDIGO DE VERIFICACIÓN: </span>
                <span style=\"font-size:30px;color:#11cdef\"><b>{$this->codigo_verify}</b></span>
                <br/><br/>
                Ahora puedes ingresa al sistema como usuario tipo \"Particular\" mediante el siguiente link:
                <a font-family:Helvetica,Arial;font-size:14px;line-height:20px;color:#478eae;text-decoration:none href=\"{$url_activa}\">Inicio de sesión aquí</a>",
            )
        )->render();

        $asunto = ($this->tipo == 'P') ? "Registro de usuario particular portal Comfaca En Linea" : "Registro de usuario portal Comfaca En Linea";
        $emailCaja = (new Mercurio01())->findFirst();
        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send($usuario->getEmail(), $html);
    }

    function generaCode()
    {
        $this->codigo_verify = genera_code();
    }
}
