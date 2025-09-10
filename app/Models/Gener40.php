<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener40 extends ModelBase
{
    protected $table = 'gener40';
    public $timestamps = false;
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'detalle',
        'orden',
    ];

    // Getters/Setters para compatibilidad legacy
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getOrden()
    {
        return $this->orden;
    }
}
