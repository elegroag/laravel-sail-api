<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b086 extends ModelBase
{
    protected $table = 'xml4b086';

    public $timestamps = false;

    protected $primaryKey = 'codgru';

    public $incrementing = false;

    protected $fillable = [
        'codgru',
        'nombre',
    ];

    // Setters
    public function setCodgru($codgru)
    {
        $this->codgru = $codgru;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters
    public function getCodgru()
    {
        return $this->codgru;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
}
