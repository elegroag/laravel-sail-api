<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio80 extends ModelBase
{
    protected $table = 'mercurio80';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'profesor',
        'colegio',
        'modain',
        'modser',
        'modjec',
        'fecha',
        'estado',
        'sede',
        'fecfin'
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setProfesor($profesor)
    {
        $this->profesor = $profesor;
    }

    public function setColegio($colegio)
    {
        $this->colegio = $colegio;
    }

    public function setModain($modain)
    {
        $this->modain = $modain;
    }

    public function setModser($modser)
    {
        $this->modser = $modser;
    }

    public function setModjec($modjec)
    {
        $this->modjec = $modjec;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setSede($sede)
    {
        $this->sede = $sede;
    }

    public function setFecfin($fecfin)
    {
        $this->fecfin = $fecfin;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProfesor()
    {
        return $this->profesor;
    }

    public function getColegio()
    {
        return $this->colegio;
    }

    public function getModain()
    {
        return $this->modain;
    }

    public function getModser()
    {
        return $this->modser;
    }

    public function getModjec()
    {
        return $this->modjec;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getSede()
    {
        return $this->sede;
    }

    public function getEstadoArray()
    {
        return [
            "A" => "ACTIVO",
            "I" => "INACTIVO"
        ];
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if($this->estado == "A") $return = "ACTIVO";
        if($this->estado == "I") $return = "INACTIVO";
        return $return;
    }

    public function getFecfin()
    {
        return $this->fecfin;
    }
}
