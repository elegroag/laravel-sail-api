<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener21 extends ModelBase
{
    protected $table = 'gener21';
    public $timestamps = false;
    protected $primaryKey = 'tipfun';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tipfun',
        'detalle',
    ];

    // Getters/Setters para compatibilidad legacy
    public function setTipfun($tipfun)
    {
        $this->tipfun = $tipfun;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function getTipfun()
    {
        return $this->tipfun;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }
}
