<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio15 extends ModelBase
{

    protected $table = 'mercurio15';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'estado',
        'detalle',
        'tipo_rural',
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
    public function getDetalle()
    {
        return $this->detalle;
    }
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }
    public function getTipoRural()
    {
        return $this->tipo_rural;
    }
    public function setTipoRural($tipo_rural)
    {
        $this->tipo_rural = $tipo_rural;
    }
}
