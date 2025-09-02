<?php

namespace App\Services\Signup;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Services\Request;
use App\Services\Utils\Comman;
use App\Services\Utils\CrearUsuario;
use App\Services\Utils\Generales;
use App\Services\Utils\SenderEmail;

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

    private $procesadorComando;
    private $crearSolicitud = false;
    private $signupEntity;
    private $codigo_verify;

    /**
     * __construct function
     * @param bool $init
     * @param SignupInterface $signupEntity
     */
    public function __construct($signupEntity = '')
    {
        $this->procesadorComando = Comman::Api();
        $this->signupEntity = $signupEntity;
    }

    public function settings(Request $params)
    {
        foreach ($params->getKeys() as $key) if (property_exists($this, $key)) $this->$key = $params->getParam($key);
    }

    /**
     * main function
     * @return SignupInterface
     */
    public function main(Request $params)
    {
        if ($params->count() > 0) $this->settings($params);

        $coddocReps = (new Mercurio30)->getCoddocreplegArray();
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
        $this->tipo = 'P';

        $this->createUserMercurio();
        $this->crearSolicitud();
        return $this->signupEntity;
    }

    /**
     * createUserMercurio function
     * @return void
     */
    public function createUserMercurio()
    {
        $this->generaCode();
        $usuarioParticular = (new Mercurio07)->findFirst("tipo='P' AND coddoc='{$this->coddoc}' AND documento='{$this->documento}'");
        $this->crearSolicitud = false;

        if ($usuarioParticular == false) {
            $out = Generales::GeneraClave();
            $hash = $out[0];
            $clave = $out[1];
            $crearUsuario = new CrearUsuario();
            $crearUsuario->setters(
                "tipo: P",
                "coddoc: {$this->coddoc}",
                "documento: {$this->documento}",
                "nombre: {$this->nombre}",
                "email: {$this->email}",
                "codciu: {$this->codciu}",
                "clave: {$hash}"
            );
            $usuarioParticular = $crearUsuario->procesar();

            $crearUsuario->crearOpcionesRecuperacion($this->codigo_verify);
            $this->crearSolicitud = true;
        } else {
            if ($usuarioParticular->getEstado() == "A") {
                throw new DebugException("El usuario ya existe y se encuentra registrado en el sistema. " .
                    "La solicitud para afiliación está pendiente de enviar, compruebe las credenciales de acceso en la dirección de correo registrada previamente: " .
                    mask_email($usuarioParticular->getEmail()) . ". <br/>" .
                    " Y ahora puedes ingresar por la opción \"2 Afiliación Pendiente\" continua el proceso de afiliación.", 501);
            } else {
                //actualiza y activa la cuenta de la persona solo si el correo es igual al reportado
                $this->crearSolicitud = true;
            }
        }
        $this->preparaMail($usuarioParticular, $clave);
    }

    /**
     * crearSolicitud function
     * @return object
     */
    public function crearSolicitud()
    {
        if ($this->crearSolicitud == false) return false;

        $empresaSisuweb = $this->buscaEmpresaSisu($this->documento);
        $entity = $this->signupEntity->findByDocumentTemp($this->documento, $this->coddoc, $this->calemp);

        //si no existe ninguna solicitud
        if ($entity->getId() == null) {
            if ($empresaSisuweb) {
                $empresaSisuweb['coddoc'] = $this->coddoc;
                $empresaSisuweb['documento'] = $this->documento;
                $empresaSisuweb['tipo'] = $this->tipo;
                $empresaSisuweb['cedtra'] = $this->documento;
                $empresaSisuweb['usuario'] = $this->usuario;
                $empresaSisuweb['tipdoc'] = $this->tipdoc;

                $this->signupEntity->createSignupService($empresaSisuweb);
            } else {
                $this->signupEntity->createSignupService(
                    array(
                        'coddoc' => $this->coddoc,
                        'documento' => $this->documento,
                        'tipo' => $this->tipo,
                        'cedrep' => $this->cedrep,
                        'cedtra' => $this->cedrep,
                        'tipdoc' => $this->tipdoc,
                        'repleg' => $this->repleg,
                        'email' => $this->email,
                        'codciu' => $this->codciu,
                        'tipper' => $this->tipper,
                        'telefono' => $this->telefono,
                        'calemp' => $this->calemp,
                        'tipsoc' => $this->tipsoc,
                        'coddocrepleg' => $this->coddocrepleg,
                        'razsoc' => $this->razsoc,
                        'usuario' => $this->usuario,
                        'tipemp' => $this->tipemp,
                        'nit' => $this->nit
                    )
                );
            }
        } else {
            throw new DebugException("Error la cuenta ya está registrada, y dispone de una solicitud en estado temporal.", 1);
        }
        $solicitud = $this->signupEntity->getSolicitud();
        $solicitud->save();
        return $solicitud;
    }

    /**
     * buscaEmpresaSisu function
     * @param integer $nit
     * @return object
     */
    function buscaEmpresaSisu($nit)
    {
        $this->procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $nit
                )
            )
        );
        if ($this->procesadorComando->isJson() == False) {
            return false;
        }
        $out = $this->procesadorComando->toArray();
        if ($out['success'] == false) return false;
        return $out['data'];
    }

    function preparaMail($usuario, $clave)
    {
        $coddoc_detalle = Generales::TipoDocumento($usuario);
        $url_activa = env("APP_URL");
        $date = new \DateTime('now');
        $html = view(
            "login/tmp/mail",
            array(
                "fecha" => date_format($date, "d - M - Y"),
                "asunto" => "Acceso a usuario particular, Comfaca En Linea",
                "tipo" => 'Usuario Particular',
                "nombre" => $this->nombre,
                "razon" => $this->razsoc,
                "msj" => "El usuario particular ha realizado el registro al portal web Comfaca En Línea.
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

        $asunto = "Registro de usuario particular portal Comfaca En Linea";
        $emailCaja = (new Mercurio01())->findFirst();
        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            array(array(
                "email" => $usuario->getEmail(),
                "nombre" => $usuario->getNombre()
            )),
            $html
        );
    }

    function generaCode()
    {
        $this->codigo_verify = "";
        $seed = str_split('1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 4) as $k) $this->codigo_verify .= $seed[$k];
    }
}
