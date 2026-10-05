<?php

namespace App\Services\Aprueba;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Api\ApiSubsidio;
use App\Services\Srequest;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

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
        $this->dominio = config('app.dominio', 'http://localhost:8000');
    }

    /**
     * procesar function
     *
     * @param array $postData
     * @return bool
     */
    public function procesar($postData)
    {
        $validacionesControl = ValidacionControlChecklist::preparar($postData, ValidacionControlChecklist::VARIANT_EMPRESA);

        $mercurio47 = Mercurio47::where('id', $this->solicitud->getId())->first();

        $ps = new ApiSubsidio;
        $ps->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $mercurio47->getDocumento(),
                    'coddoc' => $mercurio47->getCoddoc(),
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
        $empresa = $out['data'];

        $mercurio33 = Mercurio33::where('actualizacion', $this->solicitud->getId())->get();
        $dataItems = [];
        foreach ($mercurio33 as $row) {
            $dataItems[$row->getCampo()] = $row->getValor();
        }

        /**
         * la empresa se debe registrar con el tipo de documento correspondiente y no con el tipo del registro de solicitud
         */
        $params = array_merge($empresa, $dataItems, $postData, $validacionesControl);
        $ps = new ApiSubsidio;
        $ps->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'actualiza_empresa',
                'params' => $params,
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
            $message = $out['message'] ?? $out['msj'] ?? $out['error'] ?? 'Error desconocido al actualizar la empresa.';
            throw new DebugException($message, 501, ['out' => $out, 'command' => $ps->getLineaComando()]);
        }

        $registroSeguimiento = new RegistroSeguimiento;
        $registroSeguimiento->crearNota($this->tipopc, $this->solicitud->getId(), $postData['nota_aprobar'], 'A');

        $fechaEstado = $this->today->format('Y-m-d');
        Mercurio47::where('id', $this->solicitud->getId())
            ->update([
                'estado' => 'A',
                'fecest' => $fechaEstado,
                'fecapr' => $postData['fecapr'],
            ]);

        return true;
    }

    /**
     * enviarMail function
     * @return bool
     */
    public function enviarMail($actapr, $feccap)
    {
        $feccap = new DateTime($feccap);
        $dia = $feccap->format('d');
        $mes = get_mes_name($feccap->format('m'));
        $anno = $feccap->format('Y');

        $data = $this->solicitud->getArray();
        $data['membrete'] = "{$this->dominio}/public/img/header_reporte_ugpp.png";
        $data['ruta_firma'] = "{$this->dominio}Mercurio/public/img/Mercurio/firma_jefe_yenny.jpg";
        $data['actapr'] = $actapr;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['anno'] = $anno;
        $data['msj'] = 'Se informa que los datos de la empresa fueron actualizados con éxito.';

        $emailCaja = Mercurio01::first();
        $sender = new SenderEmail(
            new Srequest(
                [
                    'emisor_email' => $emailCaja->getEmail(),
                    'emisor_clave' => $emailCaja->getClave(),
                    'asunto' => 'Actualización de datos de la empresa realizada con éxito',
                ]
            )
        );

        $html = view('cajas.layouts.aprobar', $data)->render();
        $sender->send(
            $this->solicitante->getEmail(),
            $html
        );

        return true;
    }

    public function findSolicitud($idSolicitud)
    {
        $this->solicitud = Mercurio30::where('id', $idSolicitud)->first();

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
