<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio73 extends ModelBase
{
    protected $table = 'mercurio73';
    public $timestamps = false;
    protected $primaryKey = 'numedu';

    protected $fillable = [
        'numedu',
        'archivo',
        'orden',
        'url',
        'estado'
    ];

    public function setNumedu($numedu)
    {
        $this->numedu = $numedu;
    }

    public function getNumedu()
    {
        return $this->numedu;
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
