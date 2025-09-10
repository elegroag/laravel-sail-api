<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Services\Entities\EmpresaEntity;
use App\Services\Entities\ListasEntity;
use App\Services\Entities\SucursalEntity;
use App\Services\Request;
use App\Services\SatApi\SatServices;
use App\Services\Utils\Comman;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use App\Services\Utils\CrearUsuario;
use App\Services\View;
use Carbon\Carbon;
use DateTime;
use Exception;

class ApruebaEmpresa
{
    private $today;
    private $tipopc = 2;

    private $solicitante;
    private $solicitud;
    private $dominio;

    public function __construct()
    {
        $this->today = Carbon::now();
        $this->dominio = env('APP_URL', 'http://localhost:8000');
    }

    /**
     * procesar function
     * @param [type] $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $mercurio30 = (new Mercurio30)->findFirst("id='{$this->solicitud->getId()}'");
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        $tipper = ($this->solicitud->getTipdoc() == 3) ? "J" : "N";
        $params = array_merge($this->solicitud->getArray(), $postData);

        /**
         * valida indice de aportes de empresas
         */
        if (
            $params['codind'] == '49' ||
            $params['codind'] == '07' ||
            $params['codind'] == '46' ||
            $params['codind'] == '50' ||
            $params['codind'] == '14'
        ) {
            throw new Exception("Error, el indice de aportes no es valido para empresas aportantes", 501);
        }

        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['coddoc'] = $mercurio30->getTipdoc();
        $params['fecsis'] = $hoy; //fecha captura del sistema
        $params['feccam'] = $hoy; //fecha de actualizacion
        $params['estado'] = 'A';
        $params['telt'] =  $mercurio30->getCelpri();
        $params['telr'] =  $mercurio30->getTelefono();
        $params['mailr'] = $mercurio30->getEmailpri();
        $params['calsuc'] = $mercurio30->getCalemp();
        $params['nomcon'] = $mercurio30->getPriape() . ' ' . $mercurio30->getSegape();
        $params['detalle'] = $mercurio30->getRazsoc();
        $params['nomemp'] = $mercurio30->getRazsoc();
        $params['fecapr'] =  $postData['fecapr'];
        $params['observacion'] = $postData['nota_aprobar'];
        $params['totapo'] = '0';
        $params['totcon'] = '0';
        $params['tothij'] = '0';
        $params['tother'] = '0';
        $params['totpad'] = '0';
        $params['jefper'] = null;
        $params['cedpro'] = null;
        $params['nompro'] = null;
        $params['feccer'] = $hoy;
        $params['resest'] = null;
        $params['fecmer'] = null;
        $params['feccor'] = null;
        $params['valmor'] = null;
        $params['permor'] = null;
        $params['giass']  = null;
        $params['actugp'] = null;
        $params['correo'] = 'N';
        $params['traapo'] = '0';
        $params['valapo'] = '0';
        $params['tottra'] = '0';
        $params['tietra'] = '0';
        $params['tratot'] = '0';
        $params['subpla'] = $params['codsuc'];
        $params['codlis'] = $params['codsuc'];
        $params['coddiv'] = $params['codciu'];
        $params['fosfec'] = 'N';
        $params['codase'] = '09';
        $params['todmes'] = 'S';

        $empresa = new EmpresaEntity();
        $empresa->create($params);
        if (!$empresa->validate()) {
            throw new DebugException(
                "Error, no se puede crear la empresa por validación previa.",
                501,
                array(
                    'errors' => $empresa->getValidationErrors(),
                    'attributes' => $empresa->getData(),
                )
            );
        }

        $sucursal = new SucursalEntity();
        $sucursal->create($params);
        if (!$sucursal->validate()) {
            throw new DebugException(
                "Error, no se puede crear la sucursal por validación previa.",
                501,
                array(
                    'errors' => $sucursal->getValidationErrors(),
                    'attributes' => $sucursal->getData(),
                )
            );
        }

        $listas = new ListasEntity();
        $listas->create($params);
        if (!$listas->validate()) {
            throw new DebugException(
                "Error, no se puede crear la lista por validación previa.",
                501,
                array(
                    'errors' => $listas->getValidationErrors(),
                    'attributes' => $listas->getData(),
                )
            );
        }

        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "afilia_empresa",
                "params" => array(
                    'post' => array_merge($empresa->getData(), $sucursal->getData(), $listas->getData())
                )
            )
        );
        if ($ps->isJson() == false) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);
        $out = $ps->toArray();

        if (is_null($out)) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);

        if ($out['success'] == false) throw new DebugException($out['message'], 501);


        $registroSeguimiento = new RegistroSeguimiento();
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');
        /**
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = (new Mercurio07)->findFirst("coddoc='{$this->solicitud->getTipdoc()}' and documento='{$this->solicitud->getNit()}' and tipo='E'");
        $feccla = $this->solicitante->getFeccla();
        $fecreg = $this->solicitante->getFecreg();
        $fecapr = $postData['fecapr'];

        $crearUsuario = new CrearUsuario(
            new Request(
                array(
                    "tipo" => "E",
                    "coddoc" => $this->solicitud->getTipdoc(),
                    "documento" => $this->solicitud->getNit(),
                    "nombre" => $this->solicitud->getRazsoc(),
                    "email" => $this->solicitud->getEmail(),
                    "codciu" => $this->solicitud->getCodciu(),
                    "autoriza" => $this->solicitante->getAutoriza(),
                    "clave" => $this->solicitante->getClave(),
                    "fecreg" => $fecreg->getUsingFormatDefault(),
                    "feccla" => $feccla->getUsingFormatDefault(),
                    "fecapr" => $fecapr,
                )
            )
        );

        $crearUsuario->procesar();
        if ($empresa == false) {
            $code_verify = $crearUsuario->generaCode();
            $crearUsuario->crearOpcionesRecuperacion($code_verify);
        }

        /**
         * Afiliación aceptada para el servicio sat, no disponible para independientes
         */
        $resultado_tramite = 1;
        $satServices = new SatServices();
        $satServices->notificaSatEmpresas($this->solicitud, $resultado_tramite, $postData['fecafi'], '');

        /**
         * actualiza la ficha de registro
         */
        $mercurio30->setEstado('A');
        $mercurio30->setFecapr($postData['fecapr']);
        $mercurio30->setFecest($hoy);
        $mercurio30->setTipper($tipper);
        $mercurio30->save();
        return true;
    }

    /**
     * enviarMail function
     * @param [type] $mercurio30
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $feccap = new DateTime($feccap);
        $dia = $feccap->format("d");
        $mes = get_mes_name($feccap->format("m"));
        $anno = $feccap->format("Y");

        $data = $this->solicitud->getArray();
        $data['membrete'] = "{$this->dominio}Mercurio/public/img/membrete_aprueba.jpg";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;

        $emailCaja = (new Mercurio01)->findFirst();
        $sender = new SenderEmail(
            new Request(
                array(
                    "emisor_email" => $emailCaja->getEmail(),
                    "emisor_clave" => $emailCaja->getClave(),
                    "asunto" => "Afiliación de la empresa realizada con éxito, NIT {$this->solicitud->getNit()}"
                )
            )
        );
        $sender->send(
            $this->solicitante->getEmail(),
            view("layouts/aprobar", $data)->render()
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = (new Mercurio30)->findFirst("id='{$idSolicitud}'");
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst("documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'");
        return $this->solicitante;
    }
}
