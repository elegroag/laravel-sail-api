<?php

namespace App\Services\CajaServices;

use App\Models\Notificaciones;

class NotificacionService
{
    public function getNotificacionesByUser($user)
    {
        return Notificaciones::where('estado', 'P')
            ->where('user', $user)
            ->orderByDesc('dia')
            ->orderByDesc('hora')
            ->limit(5)
            ->get();
    }

    public function createNotificacion($data)
    {
        $notificacion = new Notificaciones(
            [
                'titulo' => $data['titulo'],
                'descri' => $data['descripcion'],
                'user' => $data['user'],
                'estado' => 'P',
                'result' => '',
                'dia' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'progre' => 0,
            ]

        );
        if (! $notificacion->save()) {
            $parts = [];
            foreach ($notificacion->getMessages() as $message) {
                $parts[] = (string) $message;
            }
            throw new \RuntimeException(
                $parts !== [] ? implode('; ', $parts) : 'No se pudo crear la notificación.'
            );
        }

        return $notificacion;
    }

    public function getPaginatedByUser($user, $pagina, $limit)
    {
        $pagina = max(1, (int) $pagina);
        $limit = max(1, (int) $limit);
        $offset = ($pagina - 1) * $limit;
        $notificaciones = Notificaciones::where('user', $user)
            ->orderByDesc('dia')
            ->orderByDesc('hora')
            ->offset($offset)
            ->limit($limit)
            ->get();
        $total_registros = Notificaciones::where('user', $user)->count();
        $total_pages = $limit > 0 ? (int) ceil($total_registros / $limit) : 0;

        return [
            'total_pages' => $total_pages,
            'total_registros' => $total_registros,
            'page' => $pagina,
            'data' => $notificaciones,
        ];
    }
}
