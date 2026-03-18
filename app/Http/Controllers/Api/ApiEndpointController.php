<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Entidades\ApiEndpointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador de API para gestión de Endpoints
 * 
 * Este controlador maneja las operaciones CRUD para los endpoints
 * de la API del sistema, incluyendo creación, lectura, actualización
 * y eliminación de configuraciones de endpoints.
 * 
 * @package App\Http\Controllers\Api
 */

class ApiEndpointController extends Controller
{
    /** @var ApiEndpointService Servicio de gestión de endpoints */
    protected $apiEndpointService;

    /**
     * Constructor del controlador
     * 
     * Inicializa el controlador con el servicio de endpoints
     * inyectado mediante dependency injection.
     * 
     * @param ApiEndpointService $apiEndpointService Servicio para gestionar endpoints
     */
    public function __construct(ApiEndpointService $apiEndpointService)
    {
        $this->apiEndpointService = $apiEndpointService;
    }


    /**
     * Obtener todos los endpoints de la API
     * 
     * Retorna una lista de todos los endpoints registrados en el sistema
     * con sus configuraciones y estados.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $endpoints = $this->apiEndpointService->getAllEndpoints();

        return response()->json($endpoints);
    }


    /**
     * Crear nuevo endpoint de API
     * 
     * Registra un nuevo endpoint en el sistema con su configuración
     * de servicio y conexión.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:255',
            'endpoint_name' => 'required|string|max:255',
            'connection_name' => 'nullable|string|max:255',
        ]);

        $endpoint = $this->apiEndpointService->createEndpoint($validatedData);

        return response()->json($endpoint, 201);
    }


    /**
     * Obtener endpoint específico
     * 
     * Retorna los detalles completos de un endpoint específico
     * incluyendo su configuración y estado.
     * 
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $endpoint = $this->apiEndpointService->getEndpointById($id);

        if (! $endpoint) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        return response()->json($endpoint);
    }


    /**
     * Actualizar endpoint existente
     * 
     * Actualiza la configuración de un endpoint existente en el sistema.
     * Solo se actualizan los campos proporcionados en la solicitud.
     * 
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $endpoint = $this->apiEndpointService->getEndpointById($id);

        if (! $endpoint) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        $validatedData = $request->validate([
            'service_name' => 'sometimes|required|string|max:255',
            'endpoint_name' => 'sometimes|required|string|max:255',
            'connection_name' => 'nullable|string|max:255',
        ]);

        $updatedEndpoint = $this->apiEndpointService->updateEndpoint($id, $validatedData);

        return response()->json($updatedEndpoint);
    }


    /**
     * Actualizar nombre de conexión por servicio
     * 
     * Actualiza el nombre de conexión para todos los endpoints
     * que pertenecen a un servicio específico.
     * 
     * @param Request $request Datos de la solicitud HTTP
     * @param string $serviceName Nombre del servicio a actualizar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function updateConnectionName(Request $request, $serviceName)
    {
        $validatedData = $request->validate([
            'connection_name' => 'required|string|max:255',
        ]);

        $endpoint = $this->apiEndpointService->updateConnectionName($serviceName, $validatedData['connection_name']);

        if (! $endpoint) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        return response()->json($endpoint);
    }


    /**
     * Eliminar endpoint
     * 
     * Elimina permanentemente un endpoint del sistema.
     * Esta acción es irreversible.
     * 
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $deleted = $this->apiEndpointService->deleteEndpoint($id);

        if (! $deleted) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        return response()->json(['message' => 'Endpoint deleted successfully']);
    }


    /**
     * Sincronizar endpoints por defecto
     * 
     * Sincroniza los endpoints del sistema con los valores por defecto
     * predefinidos. Esto es útil para restaurar configuraciones base.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncDefaults()
    {
        $this->apiEndpointService->syncDefaultEndpoints();

        return response()->json(['message' => 'Endpoints synced with defaults']);
    }
}
