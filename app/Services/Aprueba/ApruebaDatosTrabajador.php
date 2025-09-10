<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Utils\Comman;
use App\Services\Utils\RegistroSeguimiento;
use Carbon\Carbon;

class ApruebaDatosTrabajador
{
    private $today;
    private $tipopc = '14';

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
        $mercurio47 = (new Mercurio47())->findFirst("id='{$this->solicitud->getId()}'");


        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" => array(
                    "cedtra" => $mercurio47->getDocumento()
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
        $trabajador = $out['data'];

        $mercurio33 = (new Mercurio33())->find("actualizacion='{$this->solicitud->getId()}'");
        $dataItems = array();
        foreach ($mercurio33 as $row) {
            $dataItems[$row->getCampo()] = $row->getValor();
        }

        $postData = array_merge($trabajador, $dataItems, $postData);
        unset($postData['fecafi']);
        unset($postData['estado']);
        unset($postData['nit']);
        unset($postData['codsuc']);
        unset($postData['codlis']);
        unset($postData['giro']);
        unset($postData['codgir']);
        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "actualiza_trabajador",
                "params" => array(
                    'cedtra' => $mercurio47->getDocumento(),
                    'coddoc' => $mercurio47->getCoddoc(),
                    'post' => $postData
                )
            )
        );
        if ($ps->isJson() == false) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);
        $out = $ps->toArray();

        if (is_null($out)) throw new DebugException("Error, no hay respuesta del servidor para validación del resultado.", 1);

        if ($out['success'] == false) throw new DebugException("Erro en respuesta de la API", 501, $out);


        $registroSeguimiento = new RegistroSeguimiento();
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');

        (new Mercurio47)->updateAll("estado='A', fecha_estado='{$this->today->getUsingFormatDefault()}'", "conditions: id='{$this->solicitud->getId()}' ");
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
        $data['razsoc'] = $this->solicitante->getNombre();
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;
        $data['msj'] = "Se informa que los datos del trabajador fueron actualizados con éxito.";

        $emailCaja = (new Mercurio01)->findFirst();
        $sender = new SenderEmail(
            new Request(
                array(
                    "emisor_email" => $emailCaja->getEmail(),
                    "emisor_clave" => $emailCaja->getClave(),
                    "asunto" => "Actualización de datos del trabajador realizada con éxito"
                )
            )
        );

        $html = View::render("layouts/mail_aprobar", $data);
        $sender->send(
            $this->solicitante->getEmail(),
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = (new Mercurio47)->findFirst("id='{$idSolicitud}'");
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst("documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'");
        return $this->solicitante;
    }
}
