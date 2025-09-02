<?php

class NotificacionService  
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getNotificacionesByUser($user)
    {
        return (new Notificaciones())->find("user='{$user}'", "order: id DESC",  "limit: 50");
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
