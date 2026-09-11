<?php

namespace App\Services\SatApi;

use App\Exceptions\DebugException;
use App\Models\Mercusat02;
use App\Services\Api\ApiSubsidio;

class SatServices
{
    private $response;

    protected $procesadorComando;

    public function __construct() {}

    /**
     * notificaSatEmpresas function
     * Se da respuesta al servicio de solicitid del SAT
     *
     * @param  Mercurio30  $empresa  Mercurio30
     * @param  int  $resultado_tramite
     * @param  string  $fecafi
     * @param  string  $motivo
     * @return array
     */
    public function notificaSatEmpresas($entity, $resultado_tramite, $fecafi, $motivo = '')
    {
        try {
            $ps = new ApiSubsidio();
            $numsat02 = Mercusat02::where('id', $entity->getId())
                ->where('documento', $entity->getDocumento())
                ->where('coddoc', $entity->getCoddoc())
                ->count();

            if ($numsat02 == 0) {
                return false;
            }

            $mercusat02 = Mercusat02::where('id', $entity->getId())
                ->where('documento', $entity->getDocumento())
                ->where('coddoc', $entity->getCoddoc())
                ->first();
            $ps->send(
                [
                    'servicio' => 'Funcionalidades',
                    'metodo' => 'respuesta_notificaciones',
                    'params' => [
                        'post' => [
                            'nit' => $entity->getNit(),
                            'tipdoc' => $entity->getTipdoc(),
                            'razsoc' => $entity->getRazsoc(),
                            'fecha_efectiva_afiliacion' => $fecafi,
                            'resultado_tramite' => $resultado_tramite,
                            'numero_transaccion' => $mercusat02->getNumtrasat(),
                            'motivo_rechazo' => $motivo,
                            'serial_sat' => '0',
                        ],
                    ],
                ]
            );

            if ($ps->isJson() == false) {
                throw new DebugException('Error al dar respuesta al servicio de solicitud sat', 501);
            }
            $this->response = $ps->toArray();
        } catch (DebugException $tf) {
            $this->response = $tf->getMessage();
        }

        return $this->response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
