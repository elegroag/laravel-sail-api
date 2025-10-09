<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio58 extends ModelBase
{
    protected $table = 'mercurio58';

    public $timestamps = false;

    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'codare',
        'archivo',
        'orden',
    ];

    // Setters
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setCodare($codare)
    {
        $this->codare = $codare;
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

    public function getCodare()
    {
        return $this->codare;
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
