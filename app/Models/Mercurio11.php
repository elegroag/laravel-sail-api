<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio11 extends ModelBase
{
    protected $table = 'mercurio11';
    public $timestamps = false;
    protected $primaryKey = 'codest';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codest',
        'detalle',
    ];

    // Setters
    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    // Getters
    public function getCodest()
    {
        return $this->codest;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }
}
