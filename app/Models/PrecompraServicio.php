<?php

namespace App\Models;

use App\Services\Ecommerce\EstadoPrecompra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'items',
        'nota',
        'valor',
        'p_id_customer',
        'estado',
        'ref_payco',
        'cod_estado_epayco',
        'motivo_epayco',
        'motivo_desestimacion',
        'detalle_desestimacion',
        'fecha_precompra',
        'fecha_pago',
        'fecha_desestimacion',
        'motivo_vencida',
        'detalle_vencida',
        'fecha_vencida',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'items' => 'array',
        'fecha_precompra' => 'datetime',
        'fecha_pago' => 'datetime',
        'fecha_desestimacion' => 'datetime',
        'fecha_vencida' => 'datetime',
    ];

    /**
     * Lista normalizada de codben (items JSON o codben singular).
     *
     * @return list<string>
     */
    public function codbenList(): array
    {
        $items = $this->items;
        if (is_array($items) && count($items) > 0) {
            $list = [];
            foreach ($items as $item) {
                $codben = trim((string) (is_array($item) ? ($item['codben'] ?? '') : $item));
                if ($codben !== '' && ! in_array($codben, $list, true)) {
                    $list[] = $codben;
                }
            }
            if (count($list) > 0) {
                return $list;
            }
        }

        $codben = trim((string) ($this->codben ?? ''));

        return $codben !== '' ? [$codben] : [];
    }

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

    public function isAbandonada(): bool
    {
        return $this->estado === EstadoPrecompra::ABANDONADA;
    }

    public function isVencida(): bool
    {
        return $this->estado === EstadoPrecompra::VENCIDA;
    }

    public function isDevolucion(): bool
    {
        return $this->estado === EstadoPrecompra::DEVOLUCION;
    }

    public function getEstadoDescripcionAttribute(): string
    {
        return EstadoPrecompra::descripcion($this->estado);
    }

    public function getMotivoVencidaDescripcionAttribute(): string
    {
        if ($this->motivo_vencida === null || $this->motivo_vencida === '') {
            return '';
        }

        return EstadoPrecompra::MOTIVOS_VENCIDA[$this->motivo_vencida] ?? (string) $this->motivo_vencida;
    }

    public function transaccionesEpayco(): HasMany
    {
        return $this->hasMany(EpaycoTransaccion::class, 'precompra_id');
    }

    /**
     * Último snapshot ePayco (validación o webhook) asociado a la precompra.
     */
    public function ultimaTransaccionEpayco(): HasOne
    {
        return $this->hasOne(EpaycoTransaccion::class, 'precompra_id')->latestOfMany();
    }

    public function devolucion(): HasOne
    {
        return $this->hasOne(DevolucionPrecompra::class, 'precompra_id');
    }
}
