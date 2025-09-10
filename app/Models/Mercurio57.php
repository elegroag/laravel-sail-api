<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio57 extends ModelBase
{
    protected $table = 'mercurio57';
    public $timestamps = false;
    protected $primaryKey = 'numpro';

    protected $fillable = [
        'numpro',
        'archivo',
        'orden',
        'url',
        'estado',
    ];

    // Setters
    public function setNumpro($numpro)
    {
        $this->numpro = $numpro;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Getters
    public function getNumpro()
    {
        return $this->numpro;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getOrden()
    {
        return $this->orden;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getEstado()
    {
        return $this->estado;
    }
}
