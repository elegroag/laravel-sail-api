<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsFacultativo;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use App\Services\Api\ApiSubsidio;

class FacultativosDocuments
{
    private $params;

    private $facultativo;

    public function main()
    {
        // Catálogos relevantes
        $ciudades = ParamsFacultativo::getCiudades();
        $zonas = ParamsFacultativo::getZonas();
        $departamentos = ParamsFacultativo::getDepartamentos();
        $tipo_pago = ParamsFacultativo::getTipoPago();
        $bancos = ParamsFacultativo::getBancos();
        $tipo_cuenta = ParamsFacultativo::getTipoCuenta();
        $giro = ParamsFacultativo::getGiro();
        $codigo_giro = ParamsFacultativo::getCodigoGiro();

        // Enriquecimientos
        $ciudad_name = ($this->facultativo->codciu ?? null) ? ($ciudades[$this->facultativo->codciu] ?? $this->facultativo->codciu) : null;
        $zona_name = ($this->facultativo->codzon ?? null) ? ($zonas[$this->facultativo->codzon] ?? $this->facultativo->codzon) : null;
        $departamento_name = null;
        if (!empty($this->facultativo->codciu)) {
            $dep = substr((string) $this->facultativo->codciu, 0, 2);
            $departamento_name = $departamentos[$dep] ?? null;
        }

        $mtd = Gener18::where('coddoc', $this->facultativo->tipdoc)->first();
        $detdoc_detalle = ($mtd) ? $mtd->detdoc : 'Cedula de ciudadania';
        $detdoc_rua = ($mtd) ? $mtd->getCodrua() : 'CC';

        $tippag_detalle = ($this->facultativo->tippag ?? null) ? ($tipo_pago[$this->facultativo->tippag] ?? '') : '';
        $banco_name = ($this->facultativo->codban ?? null) ? ($bancos[$this->facultativo->codban] ?? null) : null;
        $tipo_cuenta_name = ($this->facultativo->tipcue ?? null) ? ($tipo_cuenta[$this->facultativo->tipcue] ?? null) : null;
        $giro_name = ($this->facultativo->giro ?? null) ? ($giro[$this->facultativo->giro] ?? null) : null;
        $codigo_giro_name = ($this->facultativo->codgir ?? null) ? ($codigo_giro[$this->facultativo->codgir] ?? null) : null;

        $nombre_facultativo = trim(($this->facultativo->prinom ?? '') . ' ' . ($this->facultativo->segnom ?? '') . ' ' . ($this->facultativo->priape ?? '') . ' ' . ($this->facultativo->segape ?? ''));

        // Contexto para los templates
        $context = [
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
            'nombre_facultativo' => $nombre_facultativo,
            ...$this->facultativo->toArray(),
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
        $this->facultativo = $params['facultativo'];
    }
}
