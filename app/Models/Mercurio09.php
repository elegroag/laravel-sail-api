<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio09 extends ModelBase
{
    protected $table = 'mercurio09';

    public $timestamps = false;

    protected $primaryKey = 'tipopc';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tipopc',
        'detalle',
        'dias',
    ];

    // Setters
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setDias($dias)
    {
        $this->dias = $dias;
    }

    // Getters
    public function getTipopc()
    {
        return $this->tipopc;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getDias()
    {
        return $this->dias;
    }

    public function mercurio14s()
    {
        return $this->hasMany(Mercurio14::class);
    }

    public function mercurio13s()
    {
        return $this->hasMany(Mercurio13::class);
    }

    public function mercurio08s()
    {
        return $this->hasMany(Mercurio08::class);
    }
}
