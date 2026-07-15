<?php

namespace App\Models;

use App\Services\Ecommerce\EstadoPrecompra;
use Illuminate\Database\Eloquent\Model;

class PrecompraServicio extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'precompras_servicios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documento',
        'codser',
        'numero',
        'codben',
        'nota',
        'valor',
        'estado',
        'ref_payco',
        'cod_estado_epayco',
        'motivo_epayco',
        'motivo_desestimacion',
        'detalle_desestimacion',
        'fecha_precompra',
        'fecha_pago',
        'fecha_desestimacion',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_precompra' => 'datetime',
        'fecha_pago' => 'datetime',
        'fecha_desestimacion' => 'datetime',
    ];

    public function isPendiente(): bool
    {
        return $this->estado === EstadoPrecompra::PENDIENTE;
    }

    public function isPagado(): bool
    {
        return $this->estado === EstadoPrecompra::PAGADO;
    }

    public function isDesestimado(): bool
    {
        return $this->estado === EstadoPrecompra::DESESTIMADO;
    }

    public function isRechazado(): bool
    {
        return $this->estado === EstadoPrecompra::RECHAZADO;
    }

    public function getEstadoDescripcionAttribute(): string
    {
        return EstadoPrecompra::descripcion($this->estado);
    }
}
