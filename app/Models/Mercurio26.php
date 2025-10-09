<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio26 extends ModelBase
{
    protected $table = 'mercurio26';

    public $timestamps = false;

    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'archivo',
        'nota',
        'estado',
        'tipo',
        'orden',
    ];

    /**
     * Metodo para establecer el valor del campo numero
     *
     * @param  int  $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Metodo para establecer el valor del campo archivo
     *
     * @param  string  $archivo
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    /**
     * Metodo para establecer el valor del campo nota
     *
     * @param  string  $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * Metodo para establecer el valor del campo estado
     *
     * @param  string  $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

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
     * Metodo para establecer el valor del campo orden
     *
     * @param  string  $orden
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    /**
     * Devuelve el valor del campo numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Devuelve el valor del campo archivo
     *
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Devuelve el valor del campo nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Devuelve el valor del campo estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
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
     * Devuelve el valor del campo orden
     *
     * @return string
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
