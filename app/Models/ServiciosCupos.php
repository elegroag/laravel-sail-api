<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class ServiciosCupos extends ModelBase
{

    protected $table = 'servicios_cupos';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'codser',
        'cupos',
        'estado',
        'servicio',
    ];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setCupos($cupos)
    {
        $this->cupos = $cupos;
    }

    public function getCupos()
    {
        return $this->cupos;
    }

    public function setCodser($codser)
    {
        $this->codser = $codser;
    }

    public function getCodser()
    {
        return $this->codser;
    }

    public function getServicio()
    {
        return $this->servicio;
    }

    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }
}
