<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio28 extends ModelBase
{
    protected $table = 'mercurio28';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'campo',
        'detalle',
        'orden',
    ];

    /**
     * Metodo para establecer el valor del campo tipo
     *
     * @param  string  $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo campo
     *
     * @param  string  $campo
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;
    }

    /**
     * Metodo para establecer el valor del campo detalle
     *
     * @param  string  $detalle
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    /**
     * Metodo para establecer el valor del campo orden
     *
     * @param  int  $orden
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    /**
     * Devuelve el valor del campo tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo campo
     *
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Devuelve el valor del campo detalle
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Devuelve el valor del campo orden
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
