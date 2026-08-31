<?php

namespace App\Services\Formularios\Api;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
        if (! empty($codzon)) {
            $dep = substr((string) $codzon, 0, 2);
            $departamento_name = $departamentos[$dep] ?? null;
        }

        $departamento_notify = null;
        if (! empty($codciu)) {
            $dep = substr((string) $codciu, 0, 2);
            $departamento_notify = $departamentos[$dep] ?? null;
        }

        $ciudad_name = $codciu ? ($ciudades[$codciu] ?? $codciu) : null;
        $zona_name = $codzon ? ($zonas[$codzon] ?? $codzon) : null;

        $tipdoc = $this->solicitud['tipdoc'] ?? ($this->solicitud['coddoc'] ?? ($this->empresa->tipdoc ?? ($empresaData['tipdoc'] ?? ($empresaData['coddoc'] ?? null))));
        $mtipoDocumentos = ! empty($tipdoc) ? Gener18::where('coddoc', $tipdoc)->first() : null;
        $tipo_documento = ($mtipoDocumentos) ? $mtipoDocumentos->detdoc : 'NIT';

        $coddorepleg = tipo_document_repleg_detalle();
        $data = array_merge($empresaData, $this->solicitud);
        $today = Carbon::now();
        $coddocrepleg = $this->solicitud['coddocrepleg'] ?? ($empresaData['coddocrepleg'] ?? null);
        $tipo_documento_repleg = ! empty($coddocrepleg) && isset($coddorepleg[$coddocrepleg])
            ? $coddorepleg[$coddocrepleg]
            : 'CEDULA DE CIUDADANIA';

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
            'tipo_documento_repleg' => $tipo_documento_repleg,
            'nombre_representante' => $this->solicitud['repleg'] ?? ($empresaData['repleg'] ?? null),
        ];

        $ps = new ApiPython;
        $ps->send([
            'servicio' => 'Python',
            'metodo' => 'genera-consolidado-pdf',
            'params' => [
                'templates' => $this->params['templates'],
                'output' => $this->params['output'],
                'context' => $context,
            ],
        ]);

        if ($ps->isJson() == false) {
            throw new DebugException('Error el response JSON del service PDF no es valido.');
        }
        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException('Error generando el PDF', 501, $out);
        }
        // el documento ahora llega en base64
        $data = $out['data'];
        $api_content = $data['api_content'];
        $api_filename = $data['api_filename'];
        // guarda el archivo en storage usar Storage Disk
        if (
            $api_content &&
            $api_filename &&
            is_string($api_content) &&
            is_string($api_filename)
        ) {
            Storage::disk('temp')->put($api_filename, base64_decode($api_content));
        } else {
            throw new DebugException('Error guardando el archivo', 501, $out);
        }

        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->empresa = $params['empresa'];
        $this->solicitud = $params['solicitud'];
    }
}
