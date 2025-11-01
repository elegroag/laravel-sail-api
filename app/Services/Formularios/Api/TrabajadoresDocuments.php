<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener18;
use App\Services\Api\ApiSubsidio;

class TrabajadoresDocuments
{
    private $params;

    private $trabajador;

    public function main()
    {
        $ciudades = ParamsTrabajador::getCiudades();
        $ciudad_name = ($this->trabajador->codzon) ? $ciudades[$this->trabajador->codzon] : ' FLORENCIA';

        $mresguardos = ParamsTrabajador::getResguardos();
        $resguardo_name = ($this->trabajador->resguardo_id) ? $mresguardos[$this->trabajador->resguardo_id] : 'NO APLICA';

        $metnica = ParamsTrabajador::getPertenenciaEtnicas();
        $etnica_name = ($this->trabajador->peretn) ? $metnica[$this->trabajador->peretn] : 'NO APLICA';

        $mpueblos = ParamsTrabajador::getPueblosIndigenas();
        $pueblo_name = ($this->trabajador->pub_indigena_id) ? $mpueblos[$this->trabajador->pub_indigena_id] : 'NO APLICA';

        $ocupaciones = ParamsTrabajador::getOcupaciones();

        $mtipoDocumentos = Gener18::where('coddoc', $this->trabajador->tipdoc)->first();
        $detdoc_detalle_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'CC';

        $mtipdisca = ParamsTrabajador::getTipoDiscapacidad();
        $discapacidad_name = ($this->trabajador->tipdis) ? $mtipdisca[$this->trabajador->tipdis] : 'No tiene';

        $salario = ($this->trabajador->salario) ? '$' . $this->trabajador->salario : '$0';

        $empresa_labora = ($this->trabajador->empresalab) ? $this->trabajador->empresalab : 'NO APLICA';

        $mtippga = ParamsTrabajador::getTipoPago();
        $tippag_detalle = ($this->trabajador->tippag) ? $mtippga[$this->trabajador->tippag] : '';

        $context = [
            'cedtra' => $this->trabajador->cedtra ?? null,
            'nit' => $this->trabajador->nit ?? null,
            'ciudad_name' => $ciudad_name,
            'resguardo_name' => $resguardo_name,
            'etnica_name' => $etnica_name,
            'pueblo_name' => $pueblo_name,
            'ocupaciones' => $ocupaciones,
            'detdoc_detalle' => $detdoc_detalle_trabajador,
            'discapacidad_name' => $discapacidad_name,
            'salario' => $salario,
            'empresa_labora' => $empresa_labora,
            'tippag_detalle' => $tippag_detalle,
            'detdoc_rua_trabajador' => $detdoc_rua_trabajador,
            'nombre_trabajador' => ($this->trabajador->prinom . ' ' . $this->trabajador->segnom . ' ' . $this->trabajador->priape . ' ' . $this->trabajador->segape),
            ...$this->trabajador->toArray(),
        ];

        foreach ($this->params['oficios'] as $oficio) {
            $ps = new ApiSubsidio();
            $ps->send([
                'servicio' => 'Python',
                'metodo' => 'generate-pdf',
                'params' => [
                    'template' => $oficio['template'],
                    'output' => $oficio['output'],
                    'context' => $context,
                ]
            ]);
            if ($ps->isJson() == false) {
                break;
            }
            $out = $ps->toArray();
            if ($out['success'] == false) {
                break;
            }
        }

        sleep(2);
        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->trabajador = $params['trabajador'];
    }
}
