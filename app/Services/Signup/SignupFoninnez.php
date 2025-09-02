<?php
require_once 'SignupInterface.php';

class SignupFoninnez 
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

    public function __construct()
    {
        parent::__construct();
        $this->procesadorComando = Comman::Api();
        $this->tipo = 'N';
    }

    public function settings($argv)
    {
        $numberArguments = func_num_args();
        $arguments = (object) Utils::getParams(func_get_args(), $numberArguments);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
    }

    /**
     * createUserMercurio function
     * @return void
     */
    public function createUserMercurio()
    {
        $this->generaCode();
        $usuarioParticular = $this->Mercurio07->findFirst("tipo='P' AND coddoc='{$this->coddoc}' AND documento='{$this->documento}'");
        $this->crearSolicitud = false;

        if ($usuarioParticular == false) {
            $out = Generales::GeneraClave();
            $hash = $out[0];
            $clave = $out[1];
            $crearUsuario = new CrearUsuario();
            $crearUsuario->setters(
                "tipo: N",
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
                throw new Exception("El usuario ya existe y se encuentra registrado en el sistema. " .
                    "La solicitud para afiliación está pendiente de enviar, compruebe las credenciales de acceso en la dirección de correo registrada previamente: " .
                    mask_email($usuarioParticular->getEmail()) . ". <br/>" .
                    " Y ahora puedes ingresar por la opción \"2 Afiliación Pendiente\" continua el proceso de afiliación.", 501);
            } else {
                //actualiza y activa la cuenta de la persona solo si el correo es igual al reportado
                $this->crearSolicitud = true;
            }
        }
        $this->preparaMail($usuarioParticular, $clave);
        return $usuarioParticular;
    }

    function preparaMail($usuario, $clave)
    {
        $coddoc_detalle = Generales::TipoDocumento($usuario);
        $url_activa = "{$this->hostExterno}";
        $date = new DateTime('now');
        $html = View::render(
            "login/tmp/mail",
            array(
                "fecha" => date_format($date, "d - M - Y"),
                "asunto" => "Acceso a usuario particular, Comfaca En Linea",
                "tipo" => 'Usuario Foniñez',
                "nombre" => $this->nombre,
                "razon" => $this->razsoc,
                "msj" => "El usuario de Foniñez se ha creado con éxito. 
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
        );

        $asunto = "Registro de usuario particular portal Comfaca En Linea";
        $emailCaja = $this->Mercurio01->findFirst();
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
