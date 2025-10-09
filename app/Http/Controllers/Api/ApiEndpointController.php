<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiEndpointService;
use Illuminate\Http\Request;

class ApiEndpointController extends Controller
{
    protected $apiEndpointService;

    public function __construct(ApiEndpointService $apiEndpointService)
    {
        $this->apiEndpointService = $apiEndpointService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $endpoints = $this->apiEndpointService->getAllEndpoints();

        return response()->json($endpoints);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Update only the connection name for a service.
     *
     * @param  string  $serviceName
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Sync endpoints with default values.
     *
     * @return \Illuminate\Http\Response
     */
    public function syncDefaults()
    {
        $this->apiEndpointService->syncDefaultEndpoints();

        return response()->json(['message' => 'Endpoints synced with defaults']);
    }
}
