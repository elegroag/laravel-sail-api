<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsEmpresa;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;

class ActualizadatosDocuments
{
    private $params;

    private $empresa;

    private $solicitud;


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
        $departamentos = ParamsEmpresa::getDepartamentos();
        $codciu = $this->solicitud['codciu'] ?? ($this->solicitud['ciupri'] ?? null);
        $codzon = $this->solicitud['codzon'] ?? null;

        $departamento_name = null;
        if (!empty($codzon)) {
            $dep = substr((string) $codzon, 0, 2);
            $departamento_name = $departamentos[$dep] ?? null;
        }

        $departamento_notify = null;
        if (!empty($codciu)) {
            $dep = substr((string) $codciu, 0, 2);
            $departamento_notify = $departamentos[$dep] ?? null;
        }

        $ciudad_name = $codciu ? ($ciudades[$codciu] ?? $codciu) : null;
        $zona_name = $codzon ? ($zonas[$codzon] ?? $codzon) : null;

        $mtipoDocumentos = Gener18::where('coddoc', $this->solicitud['tipdoc'])->first();
        $tipo_documento = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'NIT';

        $coddorepleg = tipo_document_repleg_detalle();
        $data = array_merge($empresaData, $this->solicitud);
        $today = Carbon::now();
        $context = [
            ...$data,
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
            'departamento_name' => $departamento_name,
            'departamento_notify' => $departamento_notify,
            'tipo_documento' => $tipo_documento,
            'nombre_representante' => $this->solicitud['repleg'],
            'tipo_documento' => ($this->solicitud['coddocrepleg']) ? $coddorepleg[$this->solicitud['coddocrepleg']] : 'CEDULA DE CIUDADANIA',
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
        $this->solicitud = $params['solicitud'];
    }
}
