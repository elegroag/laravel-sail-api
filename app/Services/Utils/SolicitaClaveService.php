<?php

class SolicitaClaveService 
{

    private $tipo;
    private $documento;
    private $coddoc;
    private $email;
    private $codigo;

    public function __construct($argv)
    {
        parent::__construct();
        $this->tipo = $argv['tipo'];
        $this->documento = $argv['documento'];
        $this->coddoc = $argv['coddoc'];
        $this->email = $argv['email'];
        $this->codigo = $argv['codigo'];
    }

    public function main()
    {
        $this->setTransa();
        $afiliado = false;
        if (
            $this->tipo == "E" ||
            $this->tipo == "O" ||
            $this->tipo == "I" ||
            $this->tipo == "F"
        ) {
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "env" => 1,
                    "params" =>  array(
                        "nit" => $this->documento
                    )
                )
            );
            if ($procesadorComando->isJson()) {
                $rqs = $procesadorComando->toArray();
                $afiliado = ($rqs['success']) ? $rqs['data'] : false;
                if ($afiliado) {
                    $sucursal_activa = false;
                    foreach ($rqs['sucursales'] as $sucursal) {
                        if ($sucursal['estado'] != 'I') {
                            $sucursal_activa = true;
                            break;
                        }
                    }
                    if ($sucursal_activa == false) {
                        throw new Exception("No es posible continuar con la solicitud, ya qué, el afiliado no se encuentra activo. " .
                            "Se requiere de validar el estado de la empresa, las sucursales y los aportes realizados a la CAJA.", 503);
                    }
                }
            }
        } elseif ($this->tipo == "T") {
            //tipo trabajadores para consultas y auto-gestión 
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "trabajador",
                    "params" => array(
                        "cedtra" => $this->documento
                    )
                )
            );

            if ($procesadorComando->isJson() == true) {
                $rqs = $procesadorComando->toArray();
                $afiliado = ($rqs['success']) ? $rqs['data'] : false;
            }
        } else {
            throw new Exception("No es posible continuar no se identifica el tipo de documento.", 503);
        }

        if (!$afiliado) {
            throw new Exception("No es posible continuar con la solicitud, ya qué, el afiliado no se encuentra actualmente " .
                "registrado en el sistema principal de subsidio que dispone la CAJA.", 503);
        }

        $cl = $this->genera_clave();
        $mclave = $cl[0];
        $pass =  $cl[1];
        $nombre = ($this->tipo == "T") ? $afiliado['priape'] . ' ' . $afiliado['segape'] . ' ' . $afiliado['prinom'] . ' ' . $afiliado['segnom'] : $afiliado['razsoc'];
        $mercurio07 = $this->Mercurio07->findFirst("tipo='{$this->tipo}' AND documento='{$this->documento}' AND coddoc='{$this->coddoc}'");
        if ($mercurio07) {
            if (trim(strtolower($mercurio07->getEmail())) != trim(strtolower($this->email))) {
                throw new Exception("La dirección de email no es igual a la que tenemos registrada." .
                    " Y por tal motivo, no se puede restablecer la clave de acceso.  El indicio de email que está registrado es: " .
                    mask_email($mercurio07->getEmail()), 503);
            }
            $this->Mercurio07->updateAll("clave='{$mclave}'", "conditions: tipo='{$this->tipo}' AND email='{$this->email}' AND documento='{$this->documento}'");
        } else {
            ///validacion extra para empresas
            if ($this->tipo == "E") {
                //otros registros previos, con otro codigo de documento coddoc, tipdoc
                $mercurio07 = $this->Mercurio07->findFirst("tipo='E' AND documento='{$this->documento}'");
                if ($mercurio07) {
                    throw new Exception("Error el tipo de documento no coincide con el registro disponible de la empresa. {$this->documento}", 503);
                }
            }
        }

        if (trim(strtolower($afiliado['email'])) != trim(strtolower($this->email))) {
            throw new Exception("La dirección de email no es igual a la que tenemos registrada." .
                " Y por tal motivo, no se puede restablecer la clave de acceso. El indicio de email que está registrado es: " . mask_email($afiliado['email']), 503);
        }

        $this->crear_usuario($nombre, $this->email, '18001', $mclave);
        $this->endTransa();

        $mtipoDocumentos = new Gener18();
        $entity = $mtipoDocumentos->findFirst(" coddoc='{$afiliado['coddoc']}'");
        $this->coddoc_detalle = ($entity == false) ? '' : $entity->getDetdoc();

        if ($this->tipo == 'E' && $afiliado != false) {
            //cruzar la empresa para comfaca en linea
            $this->crearEmpresa($afiliado);
        }


        $mercurio02 = (new Mercurio02)->findFirst();
        $arreglo = array(
            "titulo" => "Cordial saludo,<br/>Señor@ {$nombre}",
            "msj"    => "Bienvenido a {$mercurio02->getRazsoc()}, a continuación confirmamos sus datos de usuario para el ingreso a nuestro portal web. <br/>",
            "rutaImg"    => "https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png",
            "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login/index",
            "tipo_documento" => $this->coddoc_detalle,
            "documento" => $this->documento,
            "clave" => $pass,
            "mercurio02" => array(
                "razsoc"    => $mercurio02->getRazsoc(),
                "direccion" => $mercurio02->getDireccion(),
                "email"     => $mercurio02->getEmail(),
                "telefono"  => $mercurio02->getTelefono(),
                "pagweb"    => $mercurio02->getPagweb()
            )
        );

        $html = View::render("login/tmp/mail", $arreglo);
        $asunto = "Solicitud de clave sistema Comfaca En Línea";
        $email_caja = $this->Mercurio01->findFirst();
        $emisor = array(
            "email" => $email_caja->getEmail(),
            "clave" => $email_caja->getClave()
        );

        $this->send_email($emisor, $asunto, $html, array(
            array(
                "email" => $this->email,
                "nombre" => $nombre
            )
        ));

        $afiliadoUser = $this->Mercurio07->findFirst(" tipo='{$this->tipo}' AND documento='{$this->documento}' AND coddoc='{$this->coddoc}'");
        return $afiliadoUser;
    }

    function genera_clave()
    {
        $pass = "";
        $seed = str_split('abcdefghijklmnopqrstuvwxyz1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 5) as $k) $pass .= $seed[$k];
        $mclave = '';
        for ($i = 0; $i < strlen($pass); $i++) {
            if ($i % 2 != 0) {
                $x = 6;
            } else {
                $x = -4;
            }
            $mclave .= chr(ord(substr($pass, $i, 1)) + $x + 5);
        }
        return array(md5($mclave), $pass);
    }

    function send_email($emisor, $asunto, $mensaje, $destinatarios)
    {
        Core::importFromLibrary("Swift", "Swift.php");
        Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
        $smtp = new Swift_Connection_SMTP(
            "smtp.gmail.com",
            Swift_Connection_SMTP::PORT_SECURE,
            Swift_Connection_SMTP::ENC_TLS
        );
        $smtp->setUsername($emisor['email']);
        $smtp->setPassword($emisor['clave']);
        $smsj = new Swift_Message();
        $smsj->setSubject($asunto);
        $smsj->setContentType("text/html");
        $smsj->setBody($mensaje);
        $swift = new Swift($smtp);
        $email = new Swift_RecipientList();
        foreach ($destinatarios as $ai => $destinatario) {
            if ($this->production == false) {
                $destinatario['email'] = $this->email_pruebas;
            }
            $email->addTo($destinatario['email'], $destinatario['nombre']);
        }
        $swift->send($smsj, $email, new Swift_Address($emisor['email']));
    }

    function crear_usuario($repleg, $email, $codciu, $mclave)
    {
        $today = new Date();
        $mercurio07 = $this->Mercurio07->findFirst(" tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}' ");
        if ($mercurio07 == false) {
            $mercurio07 = new Mercurio07;
            $mercurio07->setTransaction(self::$transaction);
            $mercurio07->setTipo($this->tipo);
            $mercurio07->setCoddoc($this->coddoc);
            $mercurio07->setDocumento($this->documento);
            $mercurio07->setFecreg(date('Y-m-d H:i:s'));
            $mercurio07->setFeccla($today->addMonths(3));
        }

        $mercurio07->setCodciu($codciu);
        $mercurio07->setEstado("A");
        $mercurio07->setFechaSyncron(date('Y-m-d'));
        $mercurio07->setAutoriza("S");
        $mercurio07->setNombre($repleg);
        $mercurio07->setEmail($email);
        $mercurio07->setClave($mclave);

        if (!$mercurio07->save()) {
            $msj = "";
            foreach ($mercurio07->getMessages() as $m07) $msj .= $m07->getMessage() . "\n";
            throw new Exception("Error \n" . $msj, 503);
        }

        $mercurio19 = $this->Mercurio19->findFirst(" tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}'");
        if ($mercurio19 == false) {
            $mercurio19 = new Mercurio19();
            $mercurio19->setTransaction(self::$transaction);
            $mercurio19->setTipo($this->tipo);
            $mercurio19->setCoddoc($this->coddoc);
            $mercurio19->setDocumento($this->documento);
            $mercurio19->setCodigo($this->codigo);
        }

        if (!$mercurio19->save()) {
            $msj = "";
            foreach ($mercurio19->getMessages() as $m19) $msj .= $m19->getMessage() . "\n";
            throw new Exception("Error \n" . $msj, 503);
        }

        return true;
    }

    function crearEmpresa($datos)
    {
        $mercurio30 = $this->Mercurio30->findFirst(" nit='{$datos['nit']}' AND tipo='E' AND coddoc='{$datos['coddoc']}' ");
        if (!$mercurio30) {

            $usuario = 450;
            $mercurio30 = new Mercurio30();
            $mercurio30->setId(0);
            $mercurio30->setLog($datos['nit']);
            $mercurio30->setNit($datos['nit']);
            $mercurio30->setTipdoc($datos['coddoc']);
            $mercurio30->setTipper($datos['tipper']);
            $mercurio30->setPrinom($datos['prinom']);
            $mercurio30->setSegnom($datos['segnom']);
            $mercurio30->setPriape($datos['priape']);
            $mercurio30->setSegape($datos['segape']);
            $mercurio30->setMatmer($datos['matmer']);
            $mercurio30->setRazsoc($datos['razsoc']);
            $mercurio30->setSigla($datos['sigla']);
            $mercurio30->setDigver($datos['digver']);
            $mercurio30->setCalemp($datos['calemp']);
            $mercurio30->setCedrep($datos['cedrep']);
            $mercurio30->setRepleg($datos['repleg']);
            $mercurio30->setDireccion($datos['direccion']);
            $mercurio30->setCodciu($datos['codciu']);
            $mercurio30->setCodzon($datos['codzon']);
            $mercurio30->setTelefono($datos['telefono']);
            $mercurio30->setCelular($datos['telr']);
            $mercurio30->setEmail($datos['email']);
            $mercurio30->setCodact($datos['codact']);
            $mercurio30->setFecini($datos['fecafi']);
            $mercurio30->setTottra($datos['tottra']);
            $mercurio30->setValnom($datos['totapo']);
            $mercurio30->setTipsoc($datos['tipsoc']);
            $mercurio30->setCodcaj($datos['codcaj']);
            $mercurio30->setEstado("A");
            $mercurio30->setTipemp("E");
            $mercurio30->setTipo('E');
            $mercurio30->setCoddoc($datos['coddoc']);
            $mercurio30->setDocumento($datos['nit']);
            $mercurio30->setUsuario($usuario);

            if (!$mercurio30->save()) {
                $msj = "";
                foreach ($mercurio30->getMessages() as $m30) $msj .= $m30->getMessage() . "\n";
                throw new Exception("Error \n" . $msj, 503);
            }
        }
    }

    function traer_empresa($documento)
    {
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $documento
                )
            )
        );
        $data = $ps->toArray();
        if ($data['success']) {
            return $data['data'];
        } else {
            return false;
        }
    }
}
