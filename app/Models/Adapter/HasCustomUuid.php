<?php

namespace App\Models\Adapter;

use App\Models\Radicado;
use Illuminate\Support\Facades\DB;

trait HasCustomUuid
{
    /**
     * Obtiene el nombre de la columna UUID. Por defecto, 'ruuid'.
     */
    public function getCustomUuidColumn(): string
    {
        // El nombre de columna predeterminado es 'ruuid'
        return property_exists($this, 'uuidColumn') ? $this->uuidColumn : 'ruuid';
    }

    /**
     * Genera un nuevo radicado y lo asigna al modelo (proceso posterior a la creación).
     *
     * @return $this
     */
    public function regenerateUuid()
    {
        $uuidColumn = $this->getCustomUuidColumn();
        $this->{$uuidColumn} = static::generateRadicadoForModel($this);

        return $this;
    }

    /**
     * Asigna ruuid solo si está vacío (p. ej. al enviar a caja). Idempotente en reenvíos.
     * Persiste únicamente la columna ruuid para no guardar atributos temporales (item, repleg, etc.).
     *
     * @return $this
     */
    public function assignRuuidIfMissing(): static
    {
        $uuidColumn = $this->getCustomUuidColumn();

        if (filled($this->{$uuidColumn})) {
            return $this;
        }

        $this->regenerateUuid();

        static::query()->whereKey($this->getKey())->update([
            $uuidColumn => $this->{$uuidColumn},
        ]);

        $this->syncOriginalAttribute($uuidColumn);

        return $this;
    }

    /**
     * Genera un radicado y crea el registro en la tabla radicado con control de concurrencia.
     */
    protected static function generateRadicadoForModel(mixed $model): string
    {
        $tipo = static::mapModelToTipo($model);
        $vigencia = (int) now()->year;

        return DB::transaction(function () use ($tipo, $vigencia) {
            // Obtiene el último registro por tipo y vigencia con bloqueo para evitar condiciones de carrera
            $ultimo = Radicado::where('tipo', $tipo)
                ->where('vigencia', $vigencia)
                ->lockForUpdate()
                ->orderByDesc('numero')
                ->first();

            $siguiente = ($ultimo?->numero ?? 0) + 1; // consecutivo único entero

            // Construye el texto de radicado. Ajustar formato si se requiere diferente.
            $radicadoTexto = $tipo.'-'.$vigencia.'-'.str_pad($siguiente, 5, '0', STR_PAD_LEFT);

            // Crea el registro asociado en la tabla radicado
            Radicado::create([
                'vigencia' => $vigencia,
                'tipo' => $tipo,
                'numero' => $siguiente,
                'radicado' => $radicadoTexto,
            ]);

            return $radicadoTexto;
        });
    }

    /**
     * Mapea el nombre de la clase del modelo al tipo de radicado requerido.
     */
    protected static function mapModelToTipo(mixed $model): string
    {
        $name = class_basename($model);

        return match ($name) {
            'Mercurio30' => 'EMP', // empresa
            'Mercurio31' => 'TRA', // trabajador
            'Mercurio32' => 'CON', // conyuges
            'Mercurio34' => 'BEN', // beneficiario
            'Mercurio35' => 'RET', // retiro
            'Mercurio36' => 'FAC', // facultativo
            'Mercurio38' => 'PEN', // pensionado
            'Mercurio39' => 'MAD', // madres comunitarias
            'Mercurio40' => 'DOM', // servicio domestico
            'Mercurio41' => 'IND', // independiente
            'Mercurio45' => 'CER', // certificado
            'Mercurio47' => 'ACT', // actualziacion de datos
            default => 'XNA', // Valor por defecto, ajustar si es necesario
        };
    }
}
