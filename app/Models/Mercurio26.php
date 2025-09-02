<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio26 extends ModelBase
{

    protected $table = 'mercurio26';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'numero',
        'archivo',
        'nota',
        'estado',
    ];

    /**
     * Metodo para establecer el valor del campo numero
     * @param integer $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
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
     * Metodo para establecer el valor del campo nota
     * @param string $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
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
     * Devuelve el valor del campo numero
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Devuelve el valor del campo archivo
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Devuelve el valor del campo nota
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Devuelve el valor del campo estado
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }
}
