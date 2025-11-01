<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsEmpresa;
use App\Services\Api\ApiSubsidio;

class ActualizadatosDocuments
{
    private $params;

    private $empresa; // array|object

    private $campos;  // array de cambios/valores actualizados

    public function main()
    {
        // Normalizar empresa a arreglo para el contexto
        if (is_array($this->empresa)) {
            $empresaData = $this->empresa;
        } elseif (is_object($this->empresa) && method_exists($this->empresa, 'toArray')) {
            $empresaData = $this->empresa->toArray();
        } else {
            $empresaData = (array) $this->empresa;
        }

        // Catálogos mínimos para enriquecer
        $ciudades = ParamsEmpresa::getCiudades();
        $zonas = ParamsEmpresa::getZonas();

        $codciu = $empresaData['codciu'] ?? ($empresaData['ciupri'] ?? null);
        $codzon = $empresaData['codzon'] ?? null;

        $ciudad_name = $codciu ? ($ciudades[$codciu] ?? $codciu) : null;
        $zona_name = $codzon ? ($zonas[$codzon] ?? $codzon) : null;

        $context = [
            'empresa' => $empresaData,
            'campos' => $this->campos,
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
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
        $this->empresa = $params['empresa'];
        $this->campos = $params['campos'] ?? [];
    }
}
