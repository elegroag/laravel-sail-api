<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsConyuge;
use App\Library\ProcesadorComandos\ProcesadorComandos;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use Carbon\Carbon;

class ConyugesDocuments
{

    private $params;

    private $conyuge;

    private $trabajador;

    public function main()
    {
        $ciudades = ParamsConyuge::getCiudades();
        $ciudad_name = ($this->conyuge->codzon) ? $ciudades[$this->conyuge->codzon] : 'Florencia';

        $mresguardos = ParamsConyuge::getResguardos();
        $resguardo_name = ($this->conyuge->resguardo_id) ? $mresguardos[$this->conyuge->resguardo_id] : 'Ninguna';

        $metnica = ParamsConyuge::getPertenenciaEtnicas();
        $etnica_name = ($this->conyuge->peretn) ? $metnica[$this->conyuge->peretn] : 'Ninguna';

        $mpueblos = ParamsConyuge::getPueblosIndigenas();
        $pueblo_name = ($this->conyuge->pub_indigena_id) ? $mpueblos[$this->conyuge->pub_indigena_id] : 'Ninguna';

        $ocupaciones = ParamsConyuge::getOcupaciones();
        $ocupacion = ($this->conyuge->codocu) ? $ocupaciones[$this->conyuge->codocu] : 'Ninguna';

        $mtipoDocumentos = Gener18::where("coddoc", $this->conyuge->tipdoc)->first();
        $detdoc_detalle_conyuge = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_conyuge = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'CC';

        $mtipdisca = ParamsConyuge::getTipoDiscapacidad();
        $discapacidad_name = ($this->conyuge->tipdis) ? $mtipdisca[$this->conyuge->tipdis] : 'No tiene';

        $salario = ($this->conyuge->salario && $this->conyuge->salario > 0) ? '$' . $this->conyuge->salario : '$0';

        $empresa_labora = $this->conyuge->empresalab && $this->conyuge->empresalab != NULL ? $this->conyuge->empresalab : 'Ninguna';

        $mtipoDocumentos = Gener18::where("coddoc", $this->trabajador->tipdoc)->first();
        $detdoc_detalle_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_trabajador = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'CC';

        $mtippga = ParamsConyuge::getTipoPago();
        $tippag_detalle = ($this->conyuge->tippag) ? $mtippga[$this->conyuge->tippag] : '';

        $sexos = ParamsConyuge::getSexos();
        $sexo_detalle = ($this->conyuge->sexo) ? $sexos[$this->conyuge->sexo] : 'Indefinido';

        $nombre_conyuge = $this->conyuge->prinom . ' ' . $this->conyuge->segnom . ' ' . $this->conyuge->priape . ' ' . $this->conyuge->segape;

        if (
            $this->conyuge->tippag == 'T' ||
            $this->conyuge->tippag  == null ||
            $this->conyuge->tippag  == ''
        ) {
            $info_bancaria = '';
        } else {
            $mbanco = ParamsConyuge::getBancos();
            $banco = $this->conyuge->codban ? $mbanco[$this->conyuge->codban] : "";
            $tippag_detalle = $this->conyuge->tippag ? $mtippga[$this->conyuge->tippag] : "";

            $info_bancaria = "El cónyuge {$nombre_conyuge}, con tipo documento {$detdoc_detalle_conyuge} y número {$this->conyuge->cedcon},
                solicita que el pago del subsidio cuota monetaria se realice a la cuenta {$this->conyuge->numcue} del banco {$banco},
                que corresponde al medio de pago {$tippag_detalle}.";
        }

        $context_trabajador = [
            'cedtra' => $this->trabajador->cedtra,
            'tipo_documento' => $detdoc_detalle_trabajador,
            'nombre_trabajador' => $this->trabajador->prinom . ' ' . $this->trabajador->segnom . ' ' . $this->trabajador->priape . ' ' . $this->trabajador->segape,
            'nit' => $this->trabajador->nit,
            'detdoc_rua_trabajador' => $detdoc_rua_trabajador,
            ...$this->trabajador->toArray(),
        ];

        $today = Carbon::now();
        $context = [
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
            'ciudad_name' => $ciudad_name,
            'resguardo_name' => $resguardo_name,
            'etnica_name' => $etnica_name,
            'pueblo_name' => $pueblo_name,
            'ocupacion' => $ocupacion,
            'tipo_documento' => $detdoc_detalle_conyuge,
            'discapacidad_name' => $discapacidad_name,
            'salario' => $salario,
            'empresa_labora' => $empresa_labora,
            'tippag_detalle' => $tippag_detalle,
            'detdoc_rua_conyuge' => $detdoc_rua_conyuge,
            'nombre_conyuge' => $nombre_conyuge,
            'trabajador' => $context_trabajador,
            'sexo_detalle' => $sexo_detalle,
            'info_bancaria' => $info_bancaria,
            'has_subsidio' => 'N',
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
