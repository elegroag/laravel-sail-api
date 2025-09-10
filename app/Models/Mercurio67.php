<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio67 extends ModelBase
{
    protected $table = 'mercurio67';
    public $timestamps = false;
    protected $primaryKey = 'codcla';

    protected $fillable = [
        'codcla',
        'detalle',
    ];

    // Setters
    public function setCodcla($codcla)
    {
        $this->codcla = $codcla;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    // Getters
    public function getCodcla()
    {
        return $this->codcla;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }
}
