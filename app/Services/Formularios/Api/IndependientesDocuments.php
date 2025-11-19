<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use Carbon\Carbon;

class IndependientesDocuments
{
    private $params;

    private $independiente;

    private $empresa;

    private $solicitante;

    public function main()
    {
        // Catálogos principales
        $ciudades = ParamsIndependiente::getCiudades();
        $zonas = ParamsIndependiente::getZonas();
        $actividades = ParamsIndependiente::getActividades();
        $bancos = ParamsTrabajador::getBancos();
        $ocupaciones = ParamsTrabajador::getOcupaciones();

        $nivel_educativo = nivel_educativo_array();
        $estado_civil = estados_civiles_array();
        $sexos = sexos_array();
        $tipo_discapacidad = tipo_discapacidad_array();
        $tipo_pago = tipo_pago_array();
        $tipo_cuenta = tipo_cuenta_array();

        // Enriquecimientos
        $ciudad_name = ($this->independiente->codciu ?? null) ? ($ciudades[$this->independiente->codciu] ?? $this->independiente->codciu) : null;
        $zona_name = ($this->independiente->codzon ?? null) ? ($zonas[$this->independiente->codzon] ?? $this->independiente->codzon) : null;
        $actividad_name = ($this->independiente->codact ?? null) ? ($actividades[$this->independiente->codact] ?? $this->independiente->codact) : null;

        $mtd = Gener18::where('coddoc', $this->independiente->tipdoc)->first();
        $detdoc_detalle = ($mtd) ? $mtd->detdoc : 'Cedula de ciudadania';
        $detdoc_rua = ($mtd) ? $mtd->getCodrua() : 'CC';

        $discapacidad_name = ($this->independiente->tipdis ?? null) ? ($tipo_discapacidad[$this->independiente->tipdis] ?? 'No tiene') : 'No tiene';
        $tippag_detalle = ($this->independiente->tippag ?? null) ? ($tipo_pago[$this->independiente->tippag] ?? '') : '';
        $banco_name = ($this->independiente->codban ?? null) ? ($bancos[$this->independiente->codban] ?? null) : null;
        $tipo_cuenta_name = ($this->independiente->tipcue ?? null) ? ($tipo_cuenta[$this->independiente->tipcue] ?? null) : null;

        $nombre_independiente = trim(($this->independiente->prinom ?? '') . ' ' . ($this->independiente->segnom ?? '') . ' ' . ($this->independiente->priape ?? '') . ' ' . ($this->independiente->segape ?? ''));
        $nivel_educativo_detalle = ($this->independiente->nivedu ?? null) ? ($nivel_educativo[$this->independiente->nivedu] ?? null) : null;

        // Contexto para los templates
        $today = Carbon::now();
        $context = [
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
            'actividad_name' => $actividad_name,
            'nivel_educativo' => $nivel_educativo_detalle,
            'ocupaciones' => $ocupaciones,
            'estado_civil' => $estado_civil,
            'sexos' => $sexos,
            'detdoc_detalle' => $detdoc_detalle,
            'detdoc_rua' => $detdoc_rua,
            'discapacidad_name' => $discapacidad_name,
            'tippag_detalle' => $tippag_detalle,
            'banco_name' => $banco_name,
            'tipo_cuenta_name' => $tipo_cuenta_name,
            'nombre_independiente' => $nombre_independiente,
            'empresa' => $this->independiente->toArray(),
            'solicitante' => $this->solicitante->toArray(),
            'fecnac_year' => substr($this->independiente->fecnac, 0, 4),
            'fecnac_month' => substr($this->independiente->fecnac, 5, 2),
            'fecnac_day' => substr($this->independiente->fecnac, 8, 2),
            ...$this->independiente->toArray(),
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
        $this->independiente = $params['independiente'];
        $this->solicitante = $params['solicitante'];
    }
}
