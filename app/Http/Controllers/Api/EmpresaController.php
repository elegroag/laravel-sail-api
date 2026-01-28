<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpresaCollection;
use App\Http\Resources\EmpresaResource;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{

    public function index(): EmpresaCollection
    {
        $empresas = Empresa::with('trabajadores')->get();

        return new EmpresaCollection($empresas);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'rut' => 'required|string|unique:empresas,rut|max:255',
                'direccion' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'sector_economico' => 'nullable|string|max:255',
                'numero_empleados' => 'nullable|integer|min:0',
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|in:activa,inactiva',
            ]);

            $empresa = Empresa::create($validatedData);

            return (new EmpresaResource($empresa))
                ->additional([
                    'success' => true,
                    'message' => 'Empresa creada exitosamente',
                ])
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id): EmpresaResource
    {
        $empresa = Empresa::with('trabajadores.nucleosFamiliares')->findOrFail($id);

        return new EmpresaResource($empresa);
    }


    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $empresa = Empresa::findOrFail($id);

            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'rut' => 'sometimes|required|string|max:255|unique:empresas,rut,' . $id,
                'direccion' => 'sometimes|required|string|max:255',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'sector_economico' => 'nullable|string|max:255',
                'numero_empleados' => 'nullable|integer|min:0',
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|in:activa,inactiva',
            ]);

            $empresa->update($validatedData);

            return (new EmpresaResource($empresa))
                ->additional([
                    'success' => true,
                    'message' => 'Empresa actualizada exitosamente',
                ])
                ->response();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar empresa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $empresa = Empresa::findOrFail($id);
            $empresa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empresa eliminada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar empresa: ' . $e->getMessage(),
            ], 500);
        }
    }
}
