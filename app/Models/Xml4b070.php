<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b070 extends ModelBase
{
    protected $table = 'xml4b070';
    public $timestamps = false;
    protected $primaryKey = 'tipjor';
    public $incrementing = false;

    protected $fillable = [
        'tipjor',
        'nombre',
    ];

    // Setters
    public function setTipjor($tipjor)
    {
        $this->tipjor = $tipjor;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters
    public function getTipjor()
    {
        return $this->tipjor;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
}
