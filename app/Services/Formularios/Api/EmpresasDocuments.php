<?php

namespace App\Services\Formularios\Api;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use Carbon\Carbon;

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
        $tipo_documento = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'NIT';

        $coddorepleg = array_flip(tipo_document_repleg_detalle());
        $representante = [
            'nombre' => $this->empresa->repleg,
            'tipo_documento' => ($this->empresa->coddorepleg) ? $coddorepleg[$this->empresa->coddorepleg] : 'CEDULA DE CIUDADANIA',
            'cedula' => $this->empresa->cedrep,
        ];

        $today = Carbon::now();
        $context = [
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
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
            'tipo_documento' => $tipo_documento,
            'cargo' => 'NINGUNO',
            'representante' => $representante,
            ...$this->empresa->toArray(),
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

        if ($ps->isJson() == false) return false;
        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException("Error generando el PDF", 501, $out);
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
