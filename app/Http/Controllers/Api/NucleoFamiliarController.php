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
     * Obtener todos los núcleos familiares
     * 
     * Retorna una lista de todos los núcleos familiares registrados en el sistema
     * con sus trabajadores y empresas asociadas.
     * 
     * @return JsonResponse
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
                'message' => 'Error al obtener núcleos familiares: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Crear nuevo núcleo familiar
     * 
     * Registra un nuevo miembro del núcleo familiar en el sistema
     * asociado a un trabajador existente.
     * 
     * @param Request $request
     * @return JsonResponse
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
                'message' => 'Error al crear núcleo familiar: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Obtener núcleo familiar específico
     * 
     * Retorna los detalles completos de un núcleo familiar específico
     * incluyendo su trabajador y empresa asociada.
     * 
     * @param string $id
     * @return JsonResponse
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
     * Actualizar núcleo familiar existente
     * 
     * Actualiza los datos de un núcleo familiar existente en el sistema.
     * Solo se actualizan los campos proporcionados en la solicitud.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $nucleoFamiliar = NucleoFamiliar::findOrFail($id);

            $validatedData = $request->validate([
                'nombres' => 'sometimes|required|string|max:255',
                'apellidos' => 'sometimes|required|string|max:255',
                'rut' => 'sometimes|required|string|max:255|unique:nucleos_familiares,rut,' . $id,
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
                'message' => 'Error al actualizar núcleo familiar: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Eliminar núcleo familiar
     * 
     * Elimina permanentemente un núcleo familiar del sistema.
     * Esta acción es irreversible.
     * 
     * @param string $id
     * @return JsonResponse
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
                'message' => 'Error al eliminar núcleo familiar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
