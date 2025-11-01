<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsPensionado;
use App\Models\Gener18;
use App\Services\Utils\Comman;

class PensionadosDocuments
{
    private $params;

    private $pensionado;

    public function main()
    {
        // Catálogos relevantes
        $ciudades = ParamsPensionado::getCiudades();
        $zonas = ParamsPensionado::getZonas();
        $departamentos = ParamsPensionado::getDepartamentos();
        $tipo_pago = ParamsPensionado::getTipoPago();
        $bancos = ParamsPensionado::getBancos();
        $tipo_cuenta = ParamsPensionado::getTipoCuenta();
        $giro = ParamsPensionado::getGiro();
        $codigo_giro = ParamsPensionado::getCodigoGiro();

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

        $nombre_pensionado = trim(($this->pensionado->prinom ?? '').' '.($this->pensionado->segnom ?? '').' '.($this->pensionado->priape ?? '').' '.($this->pensionado->segape ?? ''));

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
            'nombre_pensionado' => $nombre_pensionado,
            ...$this->pensionado->toArray(),
        ];

        foreach ($this->params['oficios'] as $oficio) {
            $ps = Comman::Api();
            $ps->runCli([
                'servicio' => 'Python',
                'metodo' => 'generate-pdf',
                'params' => [
                    'template' => $oficio['template'],
                    'output' => $oficio['output'],
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
        }

        sleep(2);
        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->pensionado = $params['pensionado'];
    }
}
