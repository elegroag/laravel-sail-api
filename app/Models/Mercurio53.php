<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio53 extends ModelBase
{
    protected $table = 'mercurio53';

    public $timestamps = false;

    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'archivo',
        'orden',
    ];

    // Setters
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    // Getters
    public function getNumero()
    {
        return $this->numero;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getOrden()
    {
        return $this->orden;
    }
}
