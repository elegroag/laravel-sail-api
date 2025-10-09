<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio08 extends ModelBase
{
    protected $table = 'mercurio08';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'codofi',
        'tipopc',
        'usuario',
        'orden',
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
     * Metodo para establecer el valor del campo tipopc
     *
     * @param  string  $tipopc
     */
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    /**
     * Metodo para establecer el valor del campo usuario
     *
     * @param  int  $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
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
     * Devuelve el valor del campo codofi
     *
     * @return string
     */
    public function getCodofi()
    {
        return $this->codofi;
    }

    /**
     * Devuelve el valor del campo tipopc
     *
     * @return string
     */
    public function getTipopc()
    {
        return $this->tipopc;
    }

    public function getTipopcDetalle()
    {
        $foreing = $this->getMercurio09();
        if ($foreing != false) {
            return $foreing->getDetalle();
        } else {
            return '';
        }
    }

    /**
     * Devuelve el valor del campo usuario
     *
     * @return int
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getUsuarioDetalle()
    {
        $foreing = $this->getGener02();
        if ($foreing != false) {
            return $foreing->getNombre();
        } else {
            return '';
        }
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

    public function initialize()
    {
        $this->belongsTo('tipopc', 'mercurio09', 'tipopc');
        $this->belongsTo('usuario', 'gener02', 'usuario');
        $this->belongsTo('codofi', 'mercurio04', 'codofi');
    }
}
