<?php

namespace App\Services\Entidades;

use App\Models\Adapter\DbBase;
use App\Models\Notificaciones;
use App\Services\Request;

class NotificacionService
{
    protected $user;
    protected $db;

    public function __construct()
    {
        if (session()->has('documento')) {
            $this->user = session()->all();
        }
        $this->db = DbBase::rawConnect();
    }

    public function getNotificacionesByUser($user)
    {
        return (new Notificaciones)->find("user='{$user}'", "order: id DESC",  "limit: 50");
    }

    public function createNotificacion($data)
    {
        $notificacion = new Notificaciones(
            new Request(
                array(
                    'id' => null,
                    'titulo' => $data['titulo'],
                    'descri' => $data['descripcion'],
                    'user'   => $data['user'],
                    'estado' => 'P',
                    'result' => '',
                    'dia' => date('Y-m-d'),
                    'hora' => date('H:i:s'),
                    'progre' => 0
                )
            )
        );
        $notificacion->save();
        return $notificacion;
    }
}
