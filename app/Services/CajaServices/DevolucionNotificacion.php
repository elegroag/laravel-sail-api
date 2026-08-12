<?php

namespace App\Services\CajaServices;

use App\Services\Entidades\NotificacionService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Crea notificación in-app al solicitante Mercurio (Mercurio07.documento)
 * cuando Cajas devuelve una solicitud.
 */
class DevolucionNotificacion
{
    public static function notificar(object $entity, ?string $nota, string $titulo): void
    {
        try {
            $documento = self::resolveDocumento($entity);
            if ($documento === null || $documento === '') {
                Log::warning('DevolucionNotificacion: solicitud sin documento de solicitante', [
                    'titulo' => $titulo,
                    'entity' => $entity::class,
                ]);

                return;
            }

            $descripcion = trim((string) ($nota ?? ''));
            if ($descripcion === '') {
                $descripcion = 'Su solicitud ha sido devuelta para corrección. Revise el motivo en el portal Comfaca En Línea.';
            }

            (new NotificacionService)->createNotificacion([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'user' => (string) $documento,
                'asesor' => self::resolveAsesor(),
            ]);
        } catch (Throwable $e) {
            Log::warning('DevolucionNotificacion: no se pudo crear la notificación', [
                'titulo' => $titulo,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected static function resolveDocumento(object $entity): ?string
    {
        if (method_exists($entity, 'getDocumento')) {
            $documento = $entity->getDocumento();

            return $documento !== null && $documento !== '' ? (string) $documento : null;
        }

        if (isset($entity->documento) && $entity->documento !== '') {
            return (string) $entity->documento;
        }

        return null;
    }

    protected static function resolveAsesor(): string
    {
        $user = session('user');

        if (is_array($user)) {
            return (string) ($user['usuario'] ?? '');
        }

        if (is_object($user)) {
            return (string) ($user->usuario ?? '');
        }

        return '';
    }
}
