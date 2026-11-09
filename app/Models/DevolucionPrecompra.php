<?php

namespace App\Models;

use App\Services\Ecommerce\EstadoDevolucionPrecompra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevolucionPrecompra extends Model
{
    protected $table = 'devolucion_precompra';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'precompra_id',
        'estado',
        'motivo_solicitud',
        'nota_afiliado',
        'nota_gestion',
        'usuario_gestion',
        'fecha_solicitud',
        'fecha_en_tramite',
        'fecha_resolucion',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_en_tramite' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    public function precompra(): BelongsTo
    {
        return $this->belongsTo(PrecompraServicio::class, 'precompra_id');
    }

    public function getEstadoDescripcionAttribute(): string
    {
        return EstadoDevolucionPrecompra::descripcion((string) $this->estado);
    }

    public function isSolicitada(): bool
    {
        return $this->estado === EstadoDevolucionPrecompra::SOLICITADA;
    }

    public function isEnTramite(): bool
    {
        return $this->estado === EstadoDevolucionPrecompra::EN_TRAMITE;
    }

    public function isAprobado(): bool
    {
        return $this->estado === EstadoDevolucionPrecompra::APROBADO;
    }

    public function isRechazada(): bool
    {
        return $this->estado === EstadoDevolucionPrecompra::RECHAZADA;
    }

    public function estaAbierta(): bool
    {
        return $this->isSolicitada() || $this->isEnTramite();
    }
}
