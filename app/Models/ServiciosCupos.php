<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class ServiciosCupos extends ModelBase
{
    protected $table = 'servicios_cupos';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'id',
        'codser',
        'cupos',
        'servicio',
        'estado',
        'url',
    ];

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCodser($codser)
    {
        $this->codser = $codser;
    }

    public function setCupos($cupos)
    {
        $this->cupos = $cupos;
    }

    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getCodser()
    {
        return $this->codser;
    }

    public function getCupos()
    {
        return $this->cupos;
    }

    public function getServicio()
    {
        return $this->servicio;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getUrl()
    {
        return $this->url;
    }

    // Métodos auxiliares
    public function getEstadoArray()
    {
        return ['1' => 'ACTIVO', '0' => 'INACTIVO'];
    }

    public function getEstadoDetalle()
    {
        $estados = $this->getEstadoArray();

        return $estados[$this->estado] ?? '';
    }

    public function getCodserArray()
    {
        return ['F' => 'FIJO', 'A' => 'VARIABLE'];
    }

    public function getCodserDetalle()
    {
        $tipos = $this->getCodserArray();

        return $tipos[$this->codser] ?? '';
    }
}
