<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Api\ApiSubsidio;
use App\Services\Srequest;
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
        $this->dominio = config('app.dominio', 'http://localhost:8000');
    }

    /**
     * procesar function
     *
     * @param [type] $postData
     * @return bool
     */
    public function procesar($postData)
    {
        unset($postData['validaciones_control']);

        $mercurio47 = Mercurio47::where('id', $this->solicitud->getId())->first();

        $ps = new ApiSubsidio;
        $ps->send(
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
            throw new DebugException('Error, '.$out['msj'], 1);
        }
        $trabajador = $out['data'];

        $mercurio33 = Mercurio33::where('actualizacion', $this->solicitud->getId())->get();
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
        unset($postData['validaciones_control']);
        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $ps = new ApiSubsidio;
        $ps->send(
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

        Mercurio47::where('id', $this->solicitud->getId())->update([
            'estado' => 'A',
            'fecest' => $this->today,
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
        $documento = $this->solicitud->getDocumento();
        $data = [
            'razsoc' => $this->solicitante->getNombre(),
            'email' => $this->solicitante->getEmail(),
            'membrete' => "{$this->dominio}/public/img/header_reporte_ugpp.png",
            'ruta_firma' => "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg",
            'actapr' => $actapr,
            'url_activa' => '',
            'titulo' => 'Actualización de datos del trabajador, Caja De Compensación Familiar del Caquetá COMFACA',
            'msj' => "Se informa que los datos del trabajador con número de documento de identificación {$documento} fueron actualizados con éxito.",
        ];

        $html = view('emails.mail_aprobar', $data)->render();

        $emailCaja = Mercurio01::first();
        $sender = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => "Actualización de datos del trabajador realizada con éxito, identificación {$documento}",
                ]
            )
        );

        $sender->send(
            $this->solicitante->getEmail(),
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio47::where('id', $idSolicitud)->first();

        return $this->solicitud;
    }

    public function findSolicitante()
    {
        $this->solicitante = Mercurio07::where('documento', $this->solicitud->getDocumento())
            ->where('coddoc', $this->solicitud->getCoddoc())
            ->where('tipo', $this->solicitud->getTipo())
            ->first();

        return $this->solicitante;
    }
}
