<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsPensionado;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;

class PensionadosDocuments
{
    private $params;

    private $pensionado;

    private $solicitante;

    public function main()
    {
        // Catálogos relevantes
        $ciudades = ParamsPensionado::getCiudades();
        $zonas = ParamsPensionado::getZonas();
        $departamentos = ParamsPensionado::getDepartamentos();
        $codigo_giro = ParamsTrabajador::getCodigoGiro();
        $bancos = ParamsTrabajador::getBancos();
        $ocupaciones = ParamsTrabajador::getOcupaciones();

        $tipo_pago = tipo_pago_array();
        $tipo_cuenta = tipo_cuenta_array();
        $giro = giro_array();

        // Enriquecimientos
        $ciudad_name = ($this->pensionado->codciu ?? null) ? ($ciudades[$this->pensionado->codciu] ?? $this->pensionado->codciu) : null;
        $zona_name = ($this->pensionado->codzon ?? null) ? ($zonas[$this->pensionado->codzon] ?? $this->pensionado->codzon) : null;
        $departamento_name = null;
        if (!empty($this->pensionado->codciu)) {
            $dep = substr((string) $this->pensionado->codciu, 0, 2);
            $departamento_name = $departamentos[$dep] ?? null;
        }

        $mtd = Gener18::where('coddoc', $this->pensionado->tipdoc)->first();
        $detdoc_detalle = ($mtd) ? $mtd->detdoc : 'Cedula de ciudadania';
        $detdoc_rua = ($mtd) ? $mtd->getCodrua() : 'CC';

        $tippag_detalle = ($this->pensionado->tippag ?? null) ? ($tipo_pago[$this->pensionado->tippag] ?? '') : '';
        $banco_name = ($this->pensionado->codban ?? null) ? ($bancos[$this->pensionado->codban] ?? null) : null;
        $tipo_cuenta_name = ($this->pensionado->tipcue ?? null) ? ($tipo_cuenta[$this->pensionado->tipcue] ?? null) : null;
        $giro_name = ($this->pensionado->giro ?? null) ? ($giro[$this->pensionado->giro] ?? null) : null;
        $codigo_giro_name = ($this->pensionado->codgir ?? null) ? ($codigo_giro[$this->pensionado->codgir] ?? null) : null;

        $nombre_pensionado = trim(($this->pensionado->prinom ?? '') . ' ' . ($this->pensionado->segnom ?? '') . ' ' . ($this->pensionado->priape ?? '') . ' ' . ($this->pensionado->segape ?? ''));

        // Contexto para los templates
        $today = Carbon::now();
        $context = [
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
            'departamento_name' => $departamento_name,
            'detdoc_detalle' => $detdoc_detalle,
            'detdoc_rua' => $detdoc_rua,
            'tippag_detalle' => $tippag_detalle,
            'banco_name' => $banco_name,
            'tipo_cuenta_name' => $tipo_cuenta_name,
            'giro_name' => $giro_name,
            'codigo_giro_name' => $codigo_giro_name,
            'nombre_pensionado' => $nombre_pensionado,
            'ocupaciones' => $ocupaciones,
            'fecnac_year' => substr($this->pensionado->fecnac, 0, 4),
            'fecnac_month' => substr($this->pensionado->fecnac, 5, 2),
            'fecnac_day' => substr($this->pensionado->fecnac, 8, 2),
            'empresa' => $this->pensionado->toArray(),
            'solicitante' => $this->solicitante->toArray(),
            ...$this->pensionado->toArray(),
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
        $this->pensionado = $params['pensionado'];
        $this->solicitante = $params['solicitante'];
    }
}
