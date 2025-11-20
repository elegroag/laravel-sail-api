<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Mercurio30;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;

class SolicitaClaveService
{
    private $tipo;

    private $documento;

    private $coddoc;

    private $email;

    private $codigo;

    private $coddoc_detalle;

    public function __construct($argv)
    {
        $this->tipo = $argv['tipo'];
        $this->documento = $argv['documento'];
        $this->coddoc = $argv['coddoc'];
        $this->email = $argv['email'];
        $this->codigo = $argv['codigo'];
    }

    public function main()
    {
        $afiliado = false;
        if (
            $this->tipo == 'E' ||
            $this->tipo == 'O' ||
            $this->tipo == 'I' ||
            $this->tipo == 'F'
        ) {
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'env' => 1,
                    'params' => [
                        'nit' => $this->documento,
                    ],
                ]
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
                        throw new DebugException('No es posible continuar con la solicitud, ya qué, el afiliado no se encuentra activo. ' .
                            'Se requiere de validar el estado de la empresa, las sucursales y los aportes realizados a la CAJA.', 503);
                    }
                }
            }
        } elseif ($this->tipo == 'T') {
            // tipo trabajadores para consultas y auto-gestión
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador',
                    'params' => [
                        'cedtra' => $this->documento,
                    ],
                ]
            );

            if ($procesadorComando->isJson() == true) {
                $rqs = $procesadorComando->toArray();
                $afiliado = ($rqs['success']) ? $rqs['data'] : false;
            }
        } else {
            throw new DebugException('No es posible continuar no se identifica el tipo de documento.', 503);
        }

        if (! $afiliado) {
            throw new DebugException('No es posible continuar con la solicitud, ya qué, el afiliado no se encuentra actualmente ' .
                'registrado en el sistema principal de subsidio que dispone la CAJA.', 503);
        }

        $cl = $this->genera_clave();
        $mclave = $cl[0];
        $pass = $cl[1];
        $nombre = ($this->tipo == 'T') ? $afiliado['priape'] . ' ' . $afiliado['segape'] . ' ' . $afiliado['prinom'] . ' ' . $afiliado['segnom'] : $afiliado['razsoc'];
        $mercurio07 = (new Mercurio07)->findFirst("tipo='{$this->tipo}' AND documento='{$this->documento}' AND coddoc='{$this->coddoc}'");
        if ($mercurio07) {
            if (trim(strtolower($mercurio07->getEmail())) != trim(strtolower($this->email))) {
                throw new DebugException('La dirección de email no es igual a la que tenemos registrada.' .
                    ' Y por tal motivo, no se puede restablecer la clave de acceso.  El indicio de email que está registrado es: ' .
                    mask_email($mercurio07->getEmail()), 503);
            }
            Mercurio07::where('tipo', $this->tipo)
                ->where('email', $this->email)
                ->where('documento', $this->documento)
                ->update(['clave' => $mclave]);
        } else {
            // /validacion extra para empresas
            if ($this->tipo == 'E') {
                // otros registros previos, con otro codigo de documento coddoc, tipdoc
                $mercurio07 = (new Mercurio07)->findFirst("tipo='E' AND documento='{$this->documento}'");
                if ($mercurio07) {
                    throw new DebugException("Error el tipo de documento no coincide con el registro disponible de la empresa. {$this->documento}", 503);
                }
            }
        }

        if (trim(strtolower($afiliado['email'])) != trim(strtolower($this->email))) {
            throw new DebugException('La dirección de email no es igual a la que tenemos registrada.' .
                ' Y por tal motivo, no se puede restablecer la clave de acceso. El indicio de email que está registrado es: ' . mask_email($afiliado['email']), 503);
        }

        $this->crear_usuario($nombre, $this->email, '18001', $mclave);

        $mtipoDocumentos = new Gener18;
        $entity = $mtipoDocumentos->findFirst(" coddoc='{$afiliado['coddoc']}'");
        $this->coddoc_detalle = ($entity == false) ? '' : $entity->getDetdoc();

        if ($this->tipo == 'E' && $afiliado != false) {
            // cruzar la empresa para comfaca en linea
            $this->crearEmpresa($afiliado);
        }

        $mercurio02 = (new Mercurio02)->findFirst();
        $arreglo = [
            'titulo' => "Cordial saludo,<br/>Señor@ {$nombre}",
            'msj' => "Bienvenido a {$mercurio02->getRazsoc()}, a continuación confirmamos sus datos de usuario para el ingreso a nuestro portal web. <br/>",
            'rutaImg' => 'https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png',
            'url_activa' => 'https://comfacaenlinea.com.co/Mercurio/Mercurio/login/index',
            'tipo_documento' => $this->coddoc_detalle,
            'documento' => $this->documento,
            'clave' => $pass,
            'mercurio02' => [
                'razsoc' => $mercurio02->getRazsoc(),
                'direccion' => $mercurio02->getDireccion(),
                'email' => $mercurio02->getEmail(),
                'telefono' => $mercurio02->getTelefono(),
                'pagweb' => $mercurio02->getPagweb(),
            ],
        ];

        $html = view('login/tmp/mail', $arreglo)->render();

        $asunto = 'Solicitud de clave sistema Comfaca En Línea';
        $email_caja = (new Mercurio01)->findFirst();

        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$email_caja->getEmail()}",
            "emisor_clave: {$email_caja->getClave()}",
            "asunto: {$asunto}"
        );
        $senderEmail->send(
            $this->email,
            $html
        );

        $afiliadoUser = (new Mercurio07)->findFirst(" tipo='{$this->tipo}' AND documento='{$this->documento}' AND coddoc='{$this->coddoc}'");

        return $afiliadoUser;
    }

    public function genera_clave()
    {
        $pass = '';
        $seed = str_split('abcdefghijklmnopqrstuvwxyz1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 5) as $k) {
            $pass .= $seed[$k];
        }
        $mclave = '';
        for ($i = 0; $i < strlen($pass); $i++) {
            if ($i % 2 != 0) {
                $x = 6;
            } else {
                $x = -4;
            }
            $mclave .= chr(ord(substr($pass, $i, 1)) + $x + 5);
        }

        return [md5($mclave), $pass];
    }

    public function crear_usuario($repleg, $email, $codciu, $mclave)
    {
        $today = Carbon::now();
        $mercurio07 = (new Mercurio07)->findFirst(" tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}' ");
        if ($mercurio07 == false) {
            $mercurio07 = new Mercurio07;
            $mercurio07->setTipo($this->tipo);
            $mercurio07->setCoddoc($this->coddoc);
            $mercurio07->setDocumento($this->documento);
            $mercurio07->setFecreg(date('Y-m-d H:i:s'));
            $mercurio07->setFeccla($today->addMonths(3));
        }

        $mercurio07->setCodciu($codciu);
        $mercurio07->setEstado('A');
        $mercurio07->setFechaSyncron(date('Y-m-d'));
        $mercurio07->setAutoriza('S');
        $mercurio07->setNombre($repleg);
        $mercurio07->setEmail($email);
        $mercurio07->setClave($mclave);

        if (! $mercurio07->save()) {
            $msj = '';
            foreach ($mercurio07->getMessages() as $m07) {
                $msj .= $m07->getMessage() . "\n";
            }
            throw new DebugException("Error \n" . $msj, 503);
        }

        $mercurio19 = (new Mercurio19)->findFirst(" tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}'");
        if ($mercurio19 == false) {
            $mercurio19 = new Mercurio19;
            $mercurio19->setTipo($this->tipo);
            $mercurio19->setCoddoc($this->coddoc);
            $mercurio19->setDocumento($this->documento);
            $mercurio19->setCodigo($this->codigo);
        }

        if (! $mercurio19->save()) {
            $msj = '';
            foreach ($mercurio19->getMessages() as $m19) {
                $msj .= $m19->getMessage() . "\n";
            }
            throw new DebugException("Error \n" . $msj, 503);
        }

        return true;
    }

    public function crearEmpresa($datos)
    {
        $mercurio30 = (new Mercurio30)->findFirst(" nit='{$datos['nit']}' AND tipo='E' AND coddoc='{$datos['coddoc']}' ");
        if (! $mercurio30) {

            $usuario = 450;
            $mercurio30 = new Mercurio30;
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
            $mercurio30->setEstado('A');
            $mercurio30->setTipemp('E');
            $mercurio30->setTipo('E');
            $mercurio30->setCoddoc($datos['coddoc']);
            $mercurio30->setDocumento($datos['nit']);
            $mercurio30->setUsuario($usuario);

            if (! $mercurio30->save()) {
                $msj = '';
                foreach ($mercurio30->getMessages() as $m30) {
                    $msj .= $m30->getMessage() . "\n";
                }
                throw new DebugException("Error \n" . $msj, 503);
            }
        }
    }

    public function traer_empresa($documento)
    {
        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $documento,
                ],
            ]
        );
        $data = $ps->toArray();
        if ($data['success']) {
            return $data['data'];
        } else {
            return false;
        }
    }
}
