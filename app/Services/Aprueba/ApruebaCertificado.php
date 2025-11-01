<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio45;
use App\Services\Srequest;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;

class ApruebaCertificado
{
    private $today;

    private $tipopc = '8';

    private $solicitante;

    private $solicitud;

    private $dominio;

    public function __construct()
    {
        $this->today = Carbon::now();
        $this->dominio = env('APP_URL', 'http://localhost:8000');
    }

    public function procesar($postData)
    {
        $certificado = Mercurio45::where('id', $this->solicitud->getId())->first();
        $params = array_merge($certificado->getArray(), $_POST);

        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'Certificados',
                'metodo' => 'presentaCertificado',
                'params' => [
                    'post' => $params,
                ],
            ]
        );

        if ($ps->isJson() == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }
        $out = $ps->toArray();

        if (is_null($out) || $out == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 501);
        }

        if ($out['success'] == false) {
            throw new DebugException($out['message'], 501);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');
        /**
         * actualiza la ficha de registro
         */
        $certificado->setMotivo($postData['nota_aprobar']);
        $certificado->setEstado('A');
        $certificado->setFecest($this->today->format('Y-m-d'));
        $certificado->save();

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $Mercurio34
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr)
    {
        $data = [];
        $data['razsoc'] = $this->solicitante->getNombre();
        $data['email'] = $this->solicitante->getEmail();
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['url_activa'] = '';
        $data['msj'] = "Se informa que el certificado \"{$this->solicitud->getNomcer()}\" fue presentado con éxito.";
        $data['titulo'] = 'Presentación de Certificado para Aprobación';

        $html = view('layouts/mail_aprobar', $data)->render();
        $asunto = "Presentación certificado realizada con éxito, identificación {$this->solicitud->getDocumento()}";

        $emailCaja = (new Mercurio01)->findFirst();
        $senderEmail = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => $asunto,
                ]
            )
        );

        $senderEmail->send(
            $this->solicitante->getEmail(),
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = (new Mercurio45)->findFirst("id='{$idSolicitud}'");

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = (new Mercurio07)->findFirst(
            "documento='{$this->solicitud->getDocumento()}' and coddoc='{$this->solicitud->getCoddoc()}' and tipo='{$this->solicitud->getTipo()}'"
        );

        return $this->solicitante;
    }

    /**
     * deshacerAprobacion function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  int  $id
     * @param  string  $action
     * @param  string  $nota
     * @param  string  $codest
     * @param  string  $sendEmail
     * @return bool
     */
    public function deshacerAprobacion($id, $action, $nota, $codest, $sendEmail) {}
}
