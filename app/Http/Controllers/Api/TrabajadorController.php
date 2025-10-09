<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $trabajadores = Trabajador::with(['empresa', 'nucleosFamiliares'])->get();

            return response()->json([
                'success' => true,
                'data' => $trabajadores,
                'message' => 'Trabajadores obtenidos exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener trabajadores: '.$e->getMessage(),
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
                'rut' => 'required|string|unique:trabajadores,rut|max:255',
                'email' => 'required|email|unique:trabajadores,email|max:255',
                'telefono' => 'nullable|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'genero' => 'required|in:masculino,femenino,otro',
                'direccion' => 'required|string|max:255',
                'cargo' => 'required|string|max:255',
                'salario' => 'required|numeric|min:0',
                'fecha_ingreso' => 'required|date',
                'fecha_salida' => 'nullable|date|after:fecha_ingreso',
                'estado' => 'nullable|in:activo,inactivo,suspendido',
                'empresa_id' => 'required|exists:empresas,id',
            ]);

            $trabajador = Trabajador::create($validatedData);

            return response()->json([
                'success' => true,
                'data' => $trabajador->load(['empresa', 'nucleosFamiliares']),
                'message' => 'Trabajador creado exitosamente',
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
                'message' => 'Error al crear trabajador: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $trabajador = Trabajador::with(['empresa', 'nucleosFamiliares'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $trabajador,
                'message' => 'Trabajador obtenido exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajador no encontrado',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $trabajador = Trabajador::findOrFail($id);

            $validatedData = $request->validate([
                'nombres' => 'sometimes|required|string|max:255',
                'apellidos' => 'sometimes|required|string|max:255',
                'rut' => 'sometimes|required|string|max:255|unique:trabajadores,rut,'.$id,
                'email' => 'sometimes|required|email|max:255|unique:trabajadores,email,'.$id,
                'telefono' => 'nullable|string|max:255',
                'fecha_nacimiento' => 'sometimes|required|date',
                'genero' => 'sometimes|required|in:masculino,femenino,otro',
                'direccion' => 'sometimes|required|string|max:255',
                'cargo' => 'sometimes|required|string|max:255',
                'salario' => 'sometimes|required|numeric|min:0',
                'fecha_ingreso' => 'sometimes|required|date',
                'fecha_salida' => 'nullable|date|after:fecha_ingreso',
                'estado' => 'nullable|in:activo,inactivo,suspendido',
                'empresa_id' => 'sometimes|required|exists:empresas,id',
            ]);

            $trabajador->update($validatedData);

            return response()->json([
                'success' => true,
                'data' => $trabajador->load(['empresa', 'nucleosFamiliares']),
                'message' => 'Trabajador actualizado exitosamente',
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
                'message' => 'Error al actualizar trabajador: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $trabajador = Trabajador::findOrFail($id);
            $trabajador->delete();

            return response()->json([
                'success' => true,
                'message' => 'Trabajador eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar trabajador: '.$e->getMessage(),
            ], 500);
        }
    }
}
