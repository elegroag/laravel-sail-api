<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Entidades\ApiEndpointService;
use Illuminate\Http\Request;

class ApiEndpointController extends Controller
{
    protected $apiEndpointService;

    public function __construct(ApiEndpointService $apiEndpointService)
    {
        $this->apiEndpointService = $apiEndpointService;
    }


    public function index()
    {
        $endpoints = $this->apiEndpointService->getAllEndpoints();

        return response()->json($endpoints);
    }


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


    public function show($id)
    {
        $endpoint = $this->apiEndpointService->getEndpointById($id);

        if (! $endpoint) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        return response()->json($endpoint);
    }


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


    public function destroy($id)
    {
        $deleted = $this->apiEndpointService->deleteEndpoint($id);

        if (! $deleted) {
            return response()->json(['message' => 'Endpoint not found'], 404);
        }

        return response()->json(['message' => 'Endpoint deleted successfully']);
    }


    public function syncDefaults()
    {
        $this->apiEndpointService->syncDefaultEndpoints();

        return response()->json(['message' => 'Endpoints synced with defaults']);
    }
}
