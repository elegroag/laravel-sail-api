<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsConyuge;
use App\Library\ProcesadorComandos\ProcesadorComandos;
use App\Models\Gener18;
use App\Services\Api\ApiPython;

class ConyugesDocuments
{

    private $params;

    private $conyuge;

    private $trabajador;

    public function main()
    {
        $ciudades = ParamsConyuge::getCiudades();
        $ciudad_name = ($this->conyuge->codzon) ? $ciudades[$this->conyuge->codzon] : ' FLORENCIA';

        $mresguardos = ParamsConyuge::getResguardos();
        $resguardo_name = ($this->conyuge->resguardo_id) ? $mresguardos[$this->conyuge->resguardo_id] : 'NO APLICA';

        $metnica = ParamsConyuge::getPertenenciaEtnicas();
        $etnica_name = ($this->conyuge->peretn) ? $metnica[$this->conyuge->peretn] : 'NO APLICA';

        $mpueblos = ParamsConyuge::getPueblosIndigenas();
        $pueblo_name = ($this->conyuge->pub_indigena_id) ? $mpueblos[$this->conyuge->pub_indigena_id] : 'NO APLICA';

        $ocupaciones = ParamsConyuge::getOcupaciones();

        $mtipoDocumentos = Gener18::where("coddoc", $this->conyuge->tipdoc)->first();
        $detdoc_detalle_conyuge = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_conyuge = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'CC';

        $mtipdisca = ParamsConyuge::getTipoDiscapacidad();
        $discapacidad_name = ($this->conyuge->tipdis) ? $mtipdisca[$this->conyuge->tipdis] : 'No tiene';

        $salario = ($this->conyuge->salario) ? '$' . $this->conyuge->salario : '$0';

        $empresa_labora = ($this->conyuge->empresalab) ? $this->conyuge->empresalab : 'NO APLICA';

        $mtipoDocumentos = Gener18::where("coddoc", $this->trabajador->tipdoc)->first();
        $detdoc_detalle_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'CC';

        $mtippga = ParamsConyuge::getTipoPago();
        $tippag_detalle = ($this->conyuge->tippag) ? $mtippga[$this->conyuge->tippag] : '';


        $context_trabajador = [
            'cedtra' => $this->trabajador->cedtra,
            'detdoc_detalle' => $detdoc_detalle_trabajador,
            'nombre_trabajador' => $this->trabajador->prinom . ' ' . $this->trabajador->segnom . ' ' . $this->trabajador->priape . ' ' . $this->trabajador->segape,
            'nit' => $this->trabajador->nit,
            'detdoc_rua_trabajador' => $detdoc_rua_trabajador,
            ...$this->trabajador->toArray(),
        ];

        $context = [
            'ciudad_name' => $ciudad_name,
            'resguardo_name' => $resguardo_name,
            'etnica_name' => $etnica_name,
            'pueblo_name' => $pueblo_name,
            'ocupaciones' => $ocupaciones,
            'detdoc_detalle' => $detdoc_detalle_conyuge,
            'discapacidad_name' => $discapacidad_name,
            'salario' => $salario,
            'empresa_labora' => $empresa_labora,
            'tippag_detalle' => $tippag_detalle,
            'detdoc_rua_conyuge' => $detdoc_rua_conyuge,
            'nombre_conyuge' => $this->conyuge->prinom . ' ' . $this->conyuge->segnom . ' ' . $this->conyuge->priape . ' ' . $this->conyuge->segape,
            'trabajador' => $context_trabajador,
            ...$this->conyuge->toArray(),
        ];

        $ps = new ApiPython();
        $ps->send([
            'servicio' => 'Python',
            'metodo' => 'generate-pdf',
            'params' => [
                'template' => $this->params['template'],
                'output' => $this->params['output'],
                'context' => $context,
            ]
        ]);

        if ($ps->isJson() == false) {
            return false;
        }

        $out = $ps->toArray();
        if ($out['success'] == false) {
            return false;
        }

        sleep(2);
        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->conyuge = $params['conyuge'];
        $this->trabajador = $params['trabajador'];
    }
}
