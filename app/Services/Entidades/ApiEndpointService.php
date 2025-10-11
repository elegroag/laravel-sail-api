<?php

namespace App\Services;

use App\Models\ApiEndpoint;

class ApiEndpointService
{
    /**
     * Obtener todos los endpoints API.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEndpoints()
    {
        return ApiEndpoint::all();
    }

    /**
     * Obtener un endpoint por ID.
     *
     * @param  int  $id
     * @return ApiEndpoint|null
     */
    public function getEndpointById($id)
    {
        return ApiEndpoint::find($id);
    }

    /**
     * Crear un nuevo endpoint.
     *
     * @param  array  $data
     * @return ApiEndpoint
     */
    public function createEndpoint($data)
    {
        return ApiEndpoint::create($data);
    }

    /**
     * Actualizar un endpoint existente.
     *
     * @param  int  $id
     * @param  array  $data
     * @return ApiEndpoint|null
     */
    public function updateEndpoint($id, $data)
    {
        $endpoint = ApiEndpoint::find($id);

        if ($endpoint) {
            $endpoint->update($data);

            return $endpoint;
        }

        return null;
    }

    /**
     * Eliminar un endpoint.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteEndpoint($id)
    {
        $endpoint = ApiEndpoint::find($id);

        if ($endpoint) {
            return $endpoint->delete();
        }

        return false;
    }

    /**
     * Obtener endpoint por nombre de servicio.
     *
     * @param  string  $serviceName
     * @return ApiEndpoint|null
     */
    public function getEndpointByServiceName($serviceName)
    {
        return ApiEndpoint::where('service_name', $serviceName)->first();
    }

    /**
     * Actualizar nombre de conexión para un servicio.
     *
     * @param  string  $serviceName
     * @param  string  $connectionName
     * @return ApiEndpoint|null
     */
    public function updateConnectionName($serviceName, $connectionName)
    {
        $endpoint = ApiEndpoint::where('service_name', $serviceName)->first();

        if ($endpoint) {
            $endpoint->update(['connection_name' => $connectionName]);

            return $endpoint;
        }

        return null;
    }

    /**
     * Sincronizar endpoints con valores por defecto.
     *
     * @return void
     */
    public function syncDefaultEndpoints()
    {
        $defaultServices = [
            'ComfacaEmpresas' => 'company',
            'ComfacaAfilia' => 'affiliation',
            'AportesEmpresas' => 'aportes',
            'Correspondencias' => 'correspondencia',
            'Novedades' => 'novedades',
            'ServicioSat' => 'sat',
            'Tesoreria' => 'tesoreria',
            'CruzarDaviplata' => 'tippag',
            'Reprocesos' => 'sat',
            'Certificados' => 'certificados',
            'Usuarios' => 'usuarios',
            'Funcionalidades' => 'satservice',
        ];

        foreach ($defaultServices as $serviceName => $endpointName) {
            ApiEndpoint::updateOrCreate(
                [
                    'service_name' => $serviceName,
                    'endpoint_name' => $endpointName,
                ],
                [
                    'connection_name' => $endpointName,
                ]
            );
        }
    }
}
