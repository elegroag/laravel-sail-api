<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio74 extends ModelBase
{
    protected $table = 'mercurio74';

    public $timestamps = false;

    protected $primaryKey = 'numrec';

    protected $fillable = [
        'numrec',
        'archivo',
        'orden',
        'url',
        'estado',
    ];

    public function setNumrec($numrec)
    {
        $this->numrec = $numrec;
    }

    public function getNumrec()
    {
        return $this->numrec;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    public function getOrden()
    {
        return $this->orden;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getEstado()
    {
        return $this->estado;
    }
}
