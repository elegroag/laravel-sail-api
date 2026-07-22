<?php

namespace App\Services\Entidades;

use App\Models\Notificaciones;
use Illuminate\Support\Collection;

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

    /**
     * Notificaciones del usuario Mercurio (documento) con nombre del asesor (gener02).
     * Destinatario: notificaciones.user = documento del afiliado.
     * Asesor: gener02 según result (si se envió) o según user.
     */
    public function getNotificacionesMercurio(string|int $documento, int $limit = 50): Collection
    {
        $documento = (string) $documento;

        return Notificaciones::query()
            ->from('notificaciones as n')
            ->leftJoin('gener02 as g_result', 'g_result.usuario', '=', 'n.result')
            ->leftJoin('gener02 as g_user', 'g_user.usuario', '=', 'n.user')
            ->where('n.user', $documento)
            ->orderByDesc('n.dia')
            ->orderByDesc('n.hora')
            ->limit($limit)
            ->get([
                'n.id',
                'n.titulo',
                'n.descri',
                'n.user',
                'n.estado',
                'n.progre',
                'n.result',
                'n.dia',
                'n.hora',
                'g_result.nombre as asesor_result',
                'g_user.nombre as asesor_user',
            ])
            ->map(fn ($row) => $this->mapNotificacionMercurio($row));
    }

    public function countPendientesMercurio(string|int $documento): int
    {
        return Notificaciones::where('user', (string) $documento)
            ->where('estado', 'P')
            ->count();
    }

    public function createNotificacion($data)
    {
        $notificacion = new Notificaciones([
            'titulo' => $data['titulo'],
            'descri' => $data['descripcion'],
            'user' => $data['user'],
            'estado' => 'P',
            'result' => $data['asesor'] ?? ($data['result'] ?? ''),
            'dia' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'progre' => 0,
        ]);
        $notificacion->save();

        return $notificacion;
    }

    protected function mapNotificacionMercurio(object $row): object
    {
        $estado = $row->estado ?? '';

        return (object) [
            'id' => $row->id,
            'titulo' => $row->titulo ?? '',
            'descripcion' => $row->descri ?? '',
            'asesor' => $row->asesor_result ?: ($row->asesor_user ?: 'Asesor COMFACA'),
            'asesor_id' => $row->result ?: $row->user,
            'estado' => $estado,
            'estado_detalle' => $this->estadoDetalle($estado),
            'dia' => $row->dia ?? '',
            'hora' => $row->hora ?? '',
            'progre' => $row->progre ?? 0,
            'result' => $row->result ?? '',
        ];
    }

    protected function estadoDetalle(?string $estado): string
    {
        return match ($estado) {
            'P' => 'Pendiente',
            'L' => 'Leída',
            'A' => 'Atendida',
            'R' => 'Rechazada',
            default => $estado ? 'Procesada' : 'Sin estado',
        };
    }
}
