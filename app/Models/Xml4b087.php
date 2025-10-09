<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b087 extends ModelBase
{
    protected $table = 'xml4b087';

    public $timestamps = false;

    protected $primaryKey = 'codpob';

    public $incrementing = false;

    protected $fillable = [
        'codpob',
        'nombre',
    ];

    // Setters
    public function setCodpob($codpob)
    {
        $this->codpob = $codpob;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters
    public function getCodpob()
    {
        return $this->codpob;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
}
