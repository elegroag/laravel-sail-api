<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio39;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use App\Services\Utils\CrearUsuario;
use Carbon\Carbon;
use DateTime;
use Exception;

class ApruebaMadreComuni
{
    private $today;
    private $tipopc = 11;
    private $procesadorComando;
    private $solicitante;
    private $solicitud;
    private $dominio;

    public function __construct()
    {
        $this->procesadorComando = Comman::Api();
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
        $hoy = $this->today->format('Y-m-d');
        /**
         * buscar registro de la empresa
         */
        $tipper = "N";
        $params = array_merge($this->solicitud->getArray(), $postData);
        $params['estado'] = 'A';
        $params['fecest'] = null;
        $params['codest'] = null;
        $params['tipper'] = $tipper;
        $params['tipapo'] = 'O';

        /**
         * valida indice de aportes
         * 07 => aportes del 0.2% pensionados
         * 49 => aportes del 0.6% pensionados
         */
        if (($params['codind'] == '49' || $params['codind'] == '07') == False) {
            throw new Exception("Error, el indice de aportes no es valido para pensionados", 501);
        }

        /**
         * tipo de sociedad por defecto es persona natural para pensionados
         */
        $params['tipsoc'] = ($params['tipsoc'] == '') ? '06' : $params['tipsoc'];
        $params['coddoc'] = $this->solicitud->getTipdoc();

        if ($this->solicitud->getTipdoc() == 3) {
            throw new Exception("Error, el tipo documento para pensionado no puede ser tipo NIT.", 501);
        }

        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */

        $this->procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "afilia_madre_comunitaria",
                "params" => $params
            )
        );

        if ($this->procesadorComando->isJson() == false) {
            throw new Exception("Error, no hay respuesta del servidor para validación del resultado.", 501);
        }

        Debug::addVariable('Comando', $this->procesadorComando->getLineaComando());

        $out = $this->procesadorComando->toArray();

        if (is_null($out)) {
            throw new Exception("Error, no hay respuesta del servidor para validación del resultado.", 501);
        }

        if ($out['success'] == false) {
            throw new Exception($out['error'], 501);
        }

        $registroSeguimiento = new RegistroSeguimiento();
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');


        /**
         * Crea de una vez e registro, permitiendo que el usuario entre con la misma password
         * como empresa sin tener que hacer la solicitud de clave
         */
        $empresa = (new Mercurio07)->findFirst("coddoc='{$this->solicitud->getTipdoc()}' and documento='{$this->solicitud->getNit()}' and tipo='E'");
        $feccla = $this->solicitante->getFeccla();
        $fecreg = $this->solicitante->getFecreg();

        $crearUsuario = new CrearUsuario();
        $crearUsuario->setters(
            "tipo: M",
            "coddoc: {$this->solicitud->getTipdoc()}",
            "documento: {$this->solicitud->getNit()}",
            "nombre: {$this->solicitud->getRazsoc()}",
            "email: {$this->solicitud->getEmail()}",
            "codciu: {$this->solicitud->getCodciu()}",
            "autoriza: '{$this->solicitante->getAutoriza()}'",
            "clave: {$this->solicitante->getClave()}",
            "fecreg: {$fecreg->getUsingFormatDefault()}",
            "feccla: {$feccla->getUsingFormatDefault()}"
        );

        $crearUsuario->procesar();
        if ($empresa == false) {
            $code_verify = $crearUsuario->generaCode();
            $crearUsuario->crearOpcionesRecuperacion($code_verify);
        }

        /**
         * actualiza la ficha de registro
         */
        (new Mercurio39)->updateAll("estado='A', fecest='{$hoy}', tipper='{$tipper}'", "conditions: id='{$this->solicitud->getId()}'");
        return true;
    }

    /**
     * enviarMail function
     * @param [type] $Mercurio39
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );

        $feccap = new DateTime($feccap);
        $dia = $feccap->format("d");
        $mes = $meses[intval($feccap->format("m") - 1)];
        $anno = $feccap->format("Y");

        $data = $this->solicitud->getArray();
        $data['membrete'] = "{$this->dominio}public/img/membrete_aprueba.jpg";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;

        $html = view("layouts/aprobar", $data)->render();
        $asunto = "Afiliación trabajador pensionado realizada con éxito, identificación {$this->solicitud->getNit()}";
        $emailCaja = (new Mercurio01)->findFirst();
        $senderEmail = new SenderEmail();

        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(array(
            array(
                "email" => $this->solicitante->getEmail(),
                "nombre" => $this->solicitante->getNombre(),
            )
        ), $html);

        return  true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = (new Mercurio39)->findFirst("id='{$idSolicitud}'");
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst("documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'");
        return $this->solicitante;
    }
}
