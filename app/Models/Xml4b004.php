<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b004 extends ModelBase
{
    protected $table = 'xml4b004';

    public $timestamps = false;

    protected $primaryKey = 'tipide';

    public $incrementing = false;

    protected $fillable = [
        'tipide',
        'nombre',
        'coddoc',
    ];

    // Setters
    public function setTipide($tipide)
    {
        $this->tipide = $tipide;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    // Getters
    public function getTipide()
    {
        return $this->tipide;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }
}
