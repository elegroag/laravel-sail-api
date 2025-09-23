<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b005 extends ModelBase
{
    protected $table = 'xml4b005';
    public $timestamps = false;
    protected $primaryKey = 'tipgen';
    public $incrementing = false;

    protected $fillable = [
        'tipgen',
        'nombre',
        'codsex',
    ];

    // Setters
    public function setTipgen($tipgen)
    {
        $this->tipgen = $tipgen;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setCodsex($codsex)
    {
        $this->codsex = $codsex;
    }

    // Getters
    public function getTipgen()
    {
        return $this->tipgen;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCodsex()
    {
        return $this->codsex;
    }
}
