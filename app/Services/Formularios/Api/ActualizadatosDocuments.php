<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsEmpresa;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
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

        $mtipoDocumentos = Gener18::where('coddoc', $empresaData['tipdoc'])->first();
        $tipo_documento = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'NIT';

        $coddorepleg = array_flip(tipo_document_repleg_detalle());
        $representante = [
            'nombre' => $empresaData['repleg'],
            'tipo_documento' => ($empresaData['coddorepleg']) ? $coddorepleg[$empresaData['coddorepleg']] : 'CEDULA DE CIUDADANIA',
            'cedula' => $empresaData['cedrep'],
        ];

        $context = [
            ...$empresaData,
            ...$this->campos,
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
            'tipo_documento' => $tipo_documento,
            'representante' => $representante,
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
        $this->empresa = $params['empresa'];
        $this->campos = $params['campos'] ?? [];
    }
}
