<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpresaCollection;
use App\Http\Resources\EmpresaResource;
use App\Models\Empresa;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Dedoc\Scramble\Attributes\ResponseFromApiResource;
use Dedoc\Scramble\Attributes\ResponseFromApiResourceCollection;
use Dedoc\Scramble\Attributes\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

#[Tag('Empresas')]
#[Group('Empresas')]
class EmpresaController extends Controller
{

    /**
     * Obtener todas las empresas
     * 
     * Retorna una lista paginada de todas las empresas registradas en el sistema
     * con sus trabajadores asociados.
     * 
     * @return EmpresaCollection
     */
    #[ResponseFromApiResourceCollection(EmpresaResource::class, status: 200)]
    public function index(): EmpresaCollection
    {
        $empresas = Empresa::with('trabajadores')->get();

        return new EmpresaCollection($empresas);
    }

    /**
     * Crear nueva empresa
     * 
     * Registra una nueva empresa en el sistema con todos sus datos
     * básicos y de contacto.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Response(status: 201, description: 'Empresa creada exitosamente')]
    #[Response(status: 422, description: 'Error de validación')]
    #[Response(status: 500, description: 'Error interno del servidor')]
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

    /**
     * Obtener empresa específica
     * 
     * Retorna los detalles completos de una empresa específica
     * incluyendo sus trabajadores y núcleos familiares.
     * 
     * @param string $id
     * @return EmpresaResource
     */
    #[ResponseFromApiResource(EmpresaResource::class, status: 200)]
    #[Response(status: 404, description: 'Empresa no encontrada')]
    public function show(string $id): EmpresaResource
    {
        $empresa = Empresa::with('trabajadores.nucleosFamiliares')->findOrFail($id);

        return new EmpresaResource($empresa);
    }


    /**
     * Actualizar empresa existente
     * 
     * Actualiza los datos de una empresa existente en el sistema.
     * Solo se actualizan los campos proporcionados en la solicitud.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    #[Response(status: 200, description: 'Empresa actualizada exitosamente')]
    #[Response(status: 404, description: 'Empresa no encontrada')]
    #[Response(status: 422, description: 'Error de validación')]
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

    /**
     * Eliminar empresa
     * 
     * Elimina permanentemente una empresa del sistema.
     * Esta acción es irreversible y elimina todos los datos asociados.
     * 
     * @param string $id
     * @return JsonResponse
     */
    #[Response(status: 200, description: 'Empresa eliminada exitosamente')]
    #[Response(status: 404, description: 'Empresa no encontrada')]
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
