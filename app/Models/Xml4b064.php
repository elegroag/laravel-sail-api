<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b064 extends ModelBase
{
    protected $table = 'xml4b064';
    public $timestamps = false;
    protected $primaryKey = 'codare';

    protected $fillable = [
        'codare',
        'nombre',
    ];

    // Setters
    public function setCodare($codare)
    {
        $this->codare = $codare;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters
    public function getCodare()
    {
        return $this->codare;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
}
