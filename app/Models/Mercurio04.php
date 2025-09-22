<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio04 extends ModelBase
{

    protected $table = 'mercurio04';
    public $timestamps = false;
    // PK es CHAR(2) 'codofi' (no autoincremental)
    protected $primaryKey = 'codofi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codofi',
        'detalle',
        'principal',
        'estado',
    ];

    /**
     * Metodo para establecer el valor del campo codofi
     * @param string $codofi
     */
    public function setCodofi($codofi)
    {
        $this->codofi = $codofi;
    }

    /**
     * Metodo para establecer el valor del campo detalle
     * @param string $detalle
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    /**
     * Metodo para establecer el valor del campo principal
     * @param string $principal
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
    }

    /**
     * Metodo para establecer el valor del campo estado
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }


    /**
     * Devuelve el valor del campo codofi
     * @return string
     */
    public function getCodofi()
    {
        return $this->codofi;
    }

    /**
     * Devuelve el valor del campo detalle
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Devuelve el valor del campo principal
     * @return string
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    public function getPrincipalArray()
    {
        return array("S" => "SI", "N" => "NO");
    }

    public function getPrincipalDetalle()
    {
        $return = "";
        if ($this->principal == "S") $return = "SI";
        if ($this->principal == "N") $return = "NO";
        return $return;
    }


    /**
     * Devuelve el valor del campo estado
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return array("A" => "ACTIVO", "I" => "INACTIVO");
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if ($this->estado == "A") $return = "ACTIVO";
        if ($this->estado == "I") $return = "INACTIVO";
        return $return;
    }
}
