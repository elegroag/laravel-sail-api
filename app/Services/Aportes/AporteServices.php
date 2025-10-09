<?php

namespace App\Services\Aportes;

use App\Services\SatApi\SatConsultaServices;
use App\Services\Utils\Comman;

class AporteServices
{
    public function ConsultaEstadoAporte($sat15)
    {
        $tipopc = 2;
        $apiRest = Comman::Api();
        $apiRest->runCli(0, ['servicio' => 'captura_empresa', 'params' => []]);
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = [];
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = [];
            foreach ($datos_captura['calemp'] as $data) {
                $_calemp[$data['calemp']] = $data['detalle'];
            }
            $_codciu = [];
            foreach ($datos_captura['codciu'] as $data) {
                $_codciu[$data['codciu']] = $data['detalle'];
            }
            $_codzon = [];
            foreach ($datos_captura['codzon'] as $data) {
                $_codzon[$data['codzon']] = $data['detalle'];
            }
            $_codact = [];
            foreach ($datos_captura['codact'] as $data) {
                $_codact[$data['codact']] = $data['detalle'];
            }
            $_tipsoc = [];
            foreach ($datos_captura['tipsoc'] as $data) {
                $_tipsoc[$data['tipsoc']] = $data['detalle'];
            }
        }

        $col = "<div class='col-md-3 border-top border-right'>";
        $response = '';
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";
        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat15['numtraccf']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Documento Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat15['tipdocemp']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat15['numdocemp']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado Pago</label>";
        $response .= "<p class='pl-2 description'>{$sat15['estpag']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat15['resultado']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat15['mensaje']}</p>";
        $response .= '</div>';
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat15['codigo']}</p>";
        $response .= '</div>';

        $response .= '</div>';
        $satConsultaServices = new SatConsultaServices;
        $response .= $satConsultaServices->consultaSeguimientoSat($tipopc, $sat15);

        return $response;
    }
}
