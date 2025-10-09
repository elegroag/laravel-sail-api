<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio05 extends ModelBase
{
    protected $table = 'mercurio05';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'codofi',
        'codciu',
    ];

    /**
     * Metodo para establecer el valor del campo codofi
     *
     * @param  string  $codofi
     */
    public function setCodofi($codofi)
    {
        $this->codofi = $codofi;
    }

    /**
     * Metodo para establecer el valor del campo codciu
     *
     * @param  string  $codciu
     */
    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    /**
     * Devuelve el valor del campo codofi
     *
     * @return string
     */
    public function getCodofi()
    {
        return $this->codofi;
    }

    /**
     * Devuelve el valor del campo codciu
     *
     * @return string
     */
    public function getCodciu()
    {
        return $this->codciu;
    }

    public function getCodciuDetalle()
    {
        $foreing = $this->getGener08();
        if ($foreing != false) {
            return $foreing->getDetciu();
        } else {
            return '';
        }
    }

    public function ciudades()
    {
        return $this->hasMany(Gener08::class);
    }
}
