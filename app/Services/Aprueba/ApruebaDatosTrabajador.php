<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
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
     *
     * @param [type] $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $mercurio47 = Mercurio47::whereRaw("id='{$this->solicitud->getId()}'")->first();

        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $mercurio47->getDocumento(),
                ],
            ]
        );
        $out = $ps->toArray();
        if (! $out) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
        }
        if (! $out['success']) {
            throw new DebugException('Error, ' . $out['msj'], 1);
        }
        $trabajador = $out['data'];

        $mercurio33 = Mercurio33::whereRaw("actualizacion='{$this->solicitud->getId()}'")->get();
        $dataItems = [];
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
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'actualiza_trabajador',
                'params' => [
                    'cedtra' => $mercurio47->getDocumento(),
                    'coddoc' => $mercurio47->getCoddoc(),
                    'post' => $postData,
                ],
            ]
        );
        if ($ps->isJson() == false) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
        }
        $out = $ps->toArray();

        if (is_null($out)) {
            throw new DebugException('Error, no hay respuesta del servidor para validación del resultado.', 1);
        }

        if ($out['success'] == false) {
            throw new DebugException('Erro en respuesta de la API', 501, $out);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');

        Mercurio47::whereRaw("id='{$this->solicitud->getId()}'")->update([
            "estado" => 'A',
            "fecha_estado" => $this->today,
        ]);

        return true;
    }

    /**
     * enviarMail function
     *
     * @param [type] $mercurio30
     * @param [type] $actapr
     * @param [type] $feccap
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $feccap = new \DateTime($feccap);
        $dia = $feccap->format('d');
        $mes = get_mes_name($feccap->format('m'));
        $anno = $feccap->format('Y');

        $data = $this->solicitud->getArray();
        $data['razsoc'] = $this->solicitante->getNombre();
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;
        $data['msj'] = 'Se informa que los datos del trabajador fueron actualizados con éxito.';

        $emailCaja = Mercurio01::first();
        $sender = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => 'Actualización de datos del trabajador realizada con éxito',
                ]
            )
        );

        $html = View('layouts/mail_aprobar', $data)->render();
        $sender->send(
            $this->solicitante->getEmail(),
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio47::whereRaw("id='{$idSolicitud}'")->first();
        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::whereRaw("documento='{$this->solicitud->getDocumento()}' and " .
            "coddoc='{$this->solicitud->getCoddoc()}' and " .
            "tipo='{$this->solicitud->getTipo()}'")
            ->first();

        return $this->solicitante;
    }
}
