<?php

namespace App\Models\Adapter;

use App\Models\Radicado;
use Illuminate\Support\Facades\DB;

trait HasCustomUuid
{
    /**
     * Boot the trait.
     */
    protected static function bootHasCustomUuid()
    {
        static::creating(function ($model) {
            $uuidColumn = $model->getCustomUuidColumn();

            // Verifica si la columna 'ruuid' (o la definida) no está establecida
            if (empty($model->{$uuidColumn})) {
                // Genera un radicado basado en el tipo del modelo y consecutivo único
                $model->{$uuidColumn} = static::generateRadicadoForModel($model);
            }
        });
    }

    /**
     * Obtiene el nombre de la columna UUID. Por defecto, 'ruuid'.
     */
    public function getCustomUuidColumn(): string
    {
        // El nombre de columna predeterminado es 'ruuid'
        return property_exists($this, 'uuidColumn') ? $this->uuidColumn : 'ruuid';
    }

    /**
     * Genera un nuevo uuid y lo asigna al modelo.
     * * @return $this
     */
    public function regenerateUuid()
    {
        $uuidColumn = $this->getCustomUuidColumn();
        $this->{$uuidColumn} = static::generateRadicadoForModel($this);
        return $this;
    }

    /**
     * Genera un radicado y crea el registro en la tabla radicado con control de concurrencia.
     */
    protected static function generateRadicadoForModel($model): string
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
            $radicadoTexto = $tipo . '-' . $vigencia . '-' . str_pad($siguiente, 5, '0', STR_PAD_LEFT);

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
    protected static function mapModelToTipo($model): string
    {
        $name = class_basename($model);
        return match ($name) {
            'Mercurio30' => 'EMP', // empresa
            'Mercurio31' => 'TRA', // trabajador
            'Mercurio32' => 'CON', // conyuges
            'Mercurio34' => 'BEN', // beneficiario
            'Mercurio36' => 'FAC', // facultativo
            'Mercurio38' => 'PEN', // pensionado
            'Mercurio39' => 'MAD', // madres comunitarias
            'Mercurio40' => 'DOM', // servicio domestico
            'Mercurio41' => 'IND', // independiente
            'Mercurio47' => 'ACT', // actualziacion de datos
            default => 'XNA', // Valor por defecto, ajustar si es necesario
        };
    }
}
