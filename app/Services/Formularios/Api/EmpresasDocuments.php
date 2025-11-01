<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsEmpresa;
use App\Models\Gener18;
use App\Services\Api\ApiSubsidio;

class EmpresasDocuments
{
    private $params;

    private $empresa;

    public function main()
    {
        $ciudades = ParamsEmpresa::getCiudades();
        $departamentos = ParamsEmpresa::getDepartamentos();
        $zonas = ParamsEmpresa::getZonas();
        $actividades = ParamsEmpresa::getActividades();

        $ciudad_name = ($this->empresa->codciu ?? null) ? ($ciudades[$this->empresa->codciu] ?? $this->empresa->codciu) : null;
        $zona_name = ($this->empresa->codzon ?? null) ? ($zonas[$this->empresa->codzon] ?? $this->empresa->codzon) : null;
        $actividad_name = ($this->empresa->codact ?? null) ? ($actividades[$this->empresa->codact] ?? $this->empresa->codact) : null;
        $departamento_name = null;
        if (!empty($this->empresa->codciu)) {
            $dep = substr((string) $this->empresa->codciu, 0, 2);
            $departamento_name = $departamentos[$dep] ?? null;
        }

        $mtipoDocumentos = Gener18::where('coddoc', $this->empresa->tipdoc)->first();
        $detdoc_detalle_empresa = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'NIT';
        $detdoc_rua_empresa = ($mtipoDocumentos) ? $mtipoDocumentos->codrua : 'NIT';

        $context = [
            'nit' => $this->empresa->nit ?? null,
            'digver' => $this->empresa->digver ?? null,
            'razsoc' => $this->empresa->razsoc ?? null,
            'repleg' => $this->empresa->repleg ?? null,
            'tipdoc' => $this->empresa->tipdoc ?? null,
            'codciu' => $this->empresa->codciu ?? null,
            'codzon' => $this->empresa->codzon ?? null,
            'codact' => $this->empresa->codact ?? null,
            'telefono' => $this->empresa->telefono ?? null,
            'celular' => $this->empresa->celular ?? null,
            'email' => $this->empresa->email ?? null,
            'ciudad_name' => $ciudad_name,
            'zona_name' => $zona_name,
            'actividad_name' => $actividad_name,
            'departamento_name' => $departamento_name,
            'detdoc_detalle_empresa' => $detdoc_detalle_empresa,
            'detdoc_rua_empresa' => $detdoc_rua_empresa,
            ...$this->empresa->toArray(),
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
    }
}
