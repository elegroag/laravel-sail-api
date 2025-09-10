<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Request;
use App\Services\Utils\Comman;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use App\Services\View;
use Carbon\Carbon;
use DateTime;

class ApruebaDatosEmpresa
{
    private $today;
    private $tipopc = '5';
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
        $mercurio47 = (new Mercurio47)->findFirst("id='{$this->solicitud->getId()}'");

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $mercurio47->getDocumento(),
                    'coddoc' => $mercurio47->getCoddoc()
                )
            )
        );
        $out =  $ps->toArray();
        if (!$out) {
            throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);
        }
        if (!$out['success']) {
            throw new DebugException("Error, " . $out['msj'], 1);
        }
        $empresa = $out['data'];

        $mercurio33 = (new Mercurio33)->find("actualizacion='{$this->solicitud->getId()}'");
        $dataItems = array();
        foreach ($mercurio33 as $row) {
            $dataItems[$row->getCampo()] = $row->getValor();
        }

        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "actualiza_empresa",
                "params" => array(
                    'nit' => $mercurio47->getDocumento(),
                    'post' => array_merge($empresa, $dataItems, $postData)
                )
            )
        );
        if ($ps->isJson() == false) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);
        $out = $ps->toArray();

        if (is_null($out)) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);

        if ($out['success'] == false) throw new DebugException($out['message'], 501);


        $registroSeguimiento = new RegistroSeguimiento();
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');

        $fechaEstado = $this->today->format('Y-m-d');
        (new Mercurio47)->updateAll("estado='A', fecha_estado='{$fechaEstado}'", "conditions: id='{$this->solicitud->getId()}' ");
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
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;
        $data['msj'] = "Se informa que los datos de la empresa fueron actualizados con éxito.";

        $emailCaja = (new Mercurio01)->findFirst();
        $sender = new SenderEmail(
            new Request(
                array(
                    "emisor_email" => $emailCaja->getEmail(),
                    "emisor_clave" => $emailCaja->getClave(),
                    "asunto" => "Actualización de datos de la empresa realizada con éxito"
                )
            )
        );

        $html = view("layouts/mail_aprobar", $data)->render();
        $sender->send(
            array(
                array(
                    "email" => $this->solicitante->getEmail(),
                    "nombre" => $this->solicitante->getNombre(),
                )
            ),
            $html
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
