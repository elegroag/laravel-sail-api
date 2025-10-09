<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio06 extends ModelBase
{
    protected $table = 'mercurio06';

    public $timestamps = false;

    // PK es CHAR(2) 'tipo' (no autoincremental)
    protected $primaryKey = 'tipo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tipo',
        'detalle',
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
     * Metodo para establecer el valor del campo detalle
     *
     * @param  string  $detalle
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
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
     * Devuelve el valor del campo detalle
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }
}
