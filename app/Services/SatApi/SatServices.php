<?php

namespace App\Services\SatApi;

use App\Exceptions\DebugException;
use App\Services\Utils\Comman;

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
            $ps = Comman::Api();
            $numsat02 = (new Mercusat02)->count(
                '*',
                "conditions: id='{$entity->getId()}' AND documento='{$entity->getDocumento()}' AND coddoc='{$entity->getCoddoc()}'"
            );

            if ($numsat02 == 0) {
                return false;
            }

            $mercusat02 = (new Mercusat02)->findFirst("id='{$entity->getId()}' AND documento='{$entity->getDocumento()}' AND coddoc='{$entity->getCoddoc()}'");
            $ps->runCli(
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
