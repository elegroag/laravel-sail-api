<?php

namespace App\Services\Entidades;

use App\Models\Notificaciones;

class NotificacionService
{
    protected $user;

    public function __construct()
    {
        $this->user = session('user');
    }

    public function getNotificacionesByUser($usuario)
    {
        return Notificaciones::where('user', $usuario)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();
    }

    public function createNotificacion($data)
    {
        $notificacion = new Notificaciones([
            'titulo' => $data['titulo'],
            'descri' => $data['descripcion'],
            'user'   => $data['user'],
            'estado' => 'P',
            'result' => '',
            'dia' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'progre' => 0
        ]);
        $notificacion->save();
        return $notificacion;
    }
}
