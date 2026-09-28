<?php

namespace App\Http\Controllers\Cajas\Concerns;

use App\Exceptions\DebugException;
use App\Services\CajaServices\EditarSolicitudFormulario;
use App\Services\CajaServices\EditarSolicitudService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Edición de la información registrada de solicitudes pendientes.
 * Requiere que el controlador defina $tipopc y $user.
 */
trait EditaSolicitud
{
    public function editarFormulario(
        Request $request,
        EditarSolicitudService $editarSolicitudService,
        EditarSolicitudFormulario $editarSolicitudFormulario
    ): JsonResponse {
        try {
            $validated = $request->validate(['id' => 'required|integer']);
            $salida = [
                'success' => true,
                'data' => $editarSolicitudService->solicitudEditable((string) $this->tipopc, (int) $validated['id']),
                'campos' => $editarSolicitudFormulario->campos((string) $this->tipopc),
            ];
        } catch (ValidationException $err) {
            $salida = [
                'success' => false,
                'msj' => 'Se requiere el identificador de la solicitud',
                'errors' => $err->errors(),
                'code' => 422,
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        } catch (Exception $err) {
            $salida = [
                'success' => false,
                'msj' => 'No fue posible cargar la solicitud: '.$err->getMessage(),
                'code' => 500,
            ];
        }

        return response()->json($salida);
    }

    public function editarSolicitud(Request $request, EditarSolicitudService $editarSolicitudService): JsonResponse
    {
        try {
            $validated = $request->validate($editarSolicitudService->rules((string) $this->tipopc));
            $resultado = $editarSolicitudService->editar(
                (string) $this->tipopc,
                (int) $validated['id'],
                $this->datosEdicion($validated),
                (string) ($this->user['usuario'] ?? '')
            );

            $salida = [
                'success' => true,
                'msj' => $resultado['cambios']
                    ? 'La información de la solicitud se actualizó con éxito'
                    : 'No se detectaron cambios en la información de la solicitud',
                'cambios' => $resultado['cambios'],
                'data' => $resultado['data'],
            ];
        } catch (ValidationException $err) {
            $salida = [
                'success' => false,
                'msj' => 'Los datos enviados no son válidos',
                'errors' => $err->errors(),
                'code' => 422,
            ];
        } catch (DebugException $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
                'code' => $err->getCode(),
            ];
        } catch (Exception $err) {
            $salida = [
                'success' => false,
                'msj' => 'No fue posible actualizar la solicitud: '.$err->getMessage(),
                'code' => 500,
            ];
        }

        return response()->json($salida);
    }

    /**
     * Ajustes propios del flujo sobre los datos validados antes de guardar.
     */
    protected function datosEdicion(array $validated): array
    {
        return $validated;
    }
}
