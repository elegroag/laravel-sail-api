<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NucleoFamiliar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NucleoFamiliarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $nucleosFamiliares = NucleoFamiliar::with('trabajador.empresa')->get();

            return response()->json([
                'success' => true,
                'data' => $nucleosFamiliares,
                'message' => 'Núcleos familiares obtenidos exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener núcleos familiares: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'rut' => 'required|string|unique:nucleos_familiares,rut|max:255',
                'fecha_nacimiento' => 'required|date',
                'genero' => 'required|in:masculino,femenino,otro',
                'parentesco' => 'required|in:conyuge,hijo,hija,padre,madre,hermano,hermana,otro',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'direccion' => 'nullable|string|max:255',
                'estado_civil' => 'nullable|in:soltero,casado,divorciado,viudo,union_libre',
                'ocupacion' => 'nullable|string|max:255',
                'dependiente_economico' => 'nullable|boolean',
                'trabajador_id' => 'required|exists:trabajadores,id',
            ]);

            $nucleoFamiliar = NucleoFamiliar::create($validatedData);

            return response()->json([
                'success' => true,
                'data' => $nucleoFamiliar->load('trabajador.empresa'),
                'message' => 'Núcleo familiar creado exitosamente',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear núcleo familiar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $nucleoFamiliar = NucleoFamiliar::with('trabajador.empresa')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $nucleoFamiliar,
                'message' => 'Núcleo familiar obtenido exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Núcleo familiar no encontrado',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $nucleoFamiliar = NucleoFamiliar::findOrFail($id);

            $validatedData = $request->validate([
                'nombres' => 'sometimes|required|string|max:255',
                'apellidos' => 'sometimes|required|string|max:255',
                'rut' => 'sometimes|required|string|max:255|unique:nucleos_familiares,rut,'.$id,
                'fecha_nacimiento' => 'sometimes|required|date',
                'genero' => 'sometimes|required|in:masculino,femenino,otro',
                'parentesco' => 'sometimes|required|in:conyuge,hijo,hija,padre,madre,hermano,hermana,otro',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'direccion' => 'nullable|string|max:255',
                'estado_civil' => 'nullable|in:soltero,casado,divorciado,viudo,union_libre',
                'ocupacion' => 'nullable|string|max:255',
                'dependiente_economico' => 'nullable|boolean',
                'trabajador_id' => 'sometimes|required|exists:trabajadores,id',
            ]);

            $nucleoFamiliar->update($validatedData);

            return response()->json([
                'success' => true,
                'data' => $nucleoFamiliar->load('trabajador.empresa'),
                'message' => 'Núcleo familiar actualizado exitosamente',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar núcleo familiar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $nucleoFamiliar = NucleoFamiliar::findOrFail($id);
            $nucleoFamiliar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Núcleo familiar eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar núcleo familiar: '.$e->getMessage(),
            ], 500);
        }
    }
}
