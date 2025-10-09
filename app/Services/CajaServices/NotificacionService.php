<?php

namespace App\Services\CajaServices;

use App\Models\Adapter\DbBase;
use App\Models\Notificaciones;

class NotificacionService
{
    protected $db;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
    }

    public function getNotificacionesByUser($user)
    {
        return $this->db->inQueryAssoc("SELECT * FROM notificaciones WHERE estado='P' and user='{$user}' order by dia DESC, hora DESC limit 5 offset 0");
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
            dd($notificacion->getMessages());
        }

        return $notificacion;
    }

    public function getPaginatedByUser($user, $pagina, $limit)
    {
        $offset = ($pagina - 1) * $limit;
        $notificaciones = $this->db->inQueryAssoc("SELECT * FROM notificaciones WHERE user='{$user}' order by dia DESC, hora DESC limit {$limit} offset {$offset}");
        $total_registros = (new Notificaciones)->count('*', "conditions: user='{$user}'");
        $total_pages = ceil($total_registros / $limit);

        return [
            'total_pages' => $total_pages,
            'total_registros' => $total_registros,
            'page' => $pagina,
            'data' => $notificaciones,
        ];
    }
}
