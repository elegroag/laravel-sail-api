<?php

namespace App\Models\Adapter;

use Illuminate\Support\Str;

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
                // Genera un UUID ordenado (orderedUuid) para mejor rendimiento de la DB
                $model->{$uuidColumn} = (string) Str::orderedUuid();
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
        $this->{$uuidColumn} = (string) Str::orderedUuid();
        return $this;
    }
}
