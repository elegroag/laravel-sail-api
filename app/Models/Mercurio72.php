<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio72 extends ModelBase
{
    protected $table = 'mercurio72';

    public $timestamps = false;

    protected $primaryKey = 'numtur';

    protected $fillable = [
        'numtur',
        'archivo',
        'orden',
        'url',
        'estado',
    ];

    public function setNumtur($numtur)
    {
        $this->numtur = $numtur;
    }

    public function getNumtur()
    {
        return $this->numtur;
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
