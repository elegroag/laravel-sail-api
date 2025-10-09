<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b081 extends ModelBase
{
    protected $table = 'xml4b081';

    public $timestamps = false;

    protected $primaryKey = 'tipben';

    public $incrementing = false;

    protected $fillable = [
        'tipben',
        'nombre',
    ];

    // Setters
    public function setTipben($tipben)
    {
        $this->tipben = $tipben;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters
    public function getTipben()
    {
        return $this->tipben;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
}
