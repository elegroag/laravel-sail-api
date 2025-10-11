<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio12 extends ModelBase
{
    protected $table = 'mercurio12';

    public $timestamps = false;

    protected $primaryKey = 'coddoc';

    protected $fillable = [
        'coddoc',
        'detalle',
    ];

    /**
     * Metodo para establecer el valor del campo coddoc
     *
     * @param  int  $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
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
     * Devuelve el valor del campo coddoc
     *
     * @return int
     */
    public function getCoddoc()
    {
        return $this->coddoc;
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

    public function getMercurio13()
    {
        return $this->hasMany(Mercurio13::class, 'coddoc');
    }
}
