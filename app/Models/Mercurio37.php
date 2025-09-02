<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio37 extends ModelBase
{

    protected $table = 'mercurio37';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipopc',
        'numero',
        'coddoc',
        'archivo',
        'fhash',
    ];

    public function setFhash($fhash)
    {
        $this->fhash = $fhash;
    }

    public function getFhash()
    {
        return $this->fhash;
    }


    /**
     * Metodo para establecer el valor del campo tipopc
     * @param string $tipopc
     */
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    /**
     * Metodo para establecer el valor del campo numero
     * @param integer $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     * @param integer $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo archivo
     * @param string $archivo
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }


    /**
     * Devuelve el valor del campo tipopc
     * @return string
     */
    public function getTipopc()
    {
        return $this->tipopc;
    }

    /**
     * Devuelve el valor del campo numero
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Devuelve el valor del campo coddoc
     * @return integer
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo archivo
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }
}
