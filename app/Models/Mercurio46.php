<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio46 extends ModelBase
{

    protected $table = 'mercurio46';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'nit',
        'fecsis',
        'archivo',
    ];

    /**
     * Metodo para establecer el valor del campo codapl
     * @param string $codapl
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Metodo para establecer el valor del campo email
     * @param string $email
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Metodo para establecer el valor del campo clave
     * @param string $clave
     */
    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    /**
     * Metodo para establecer el valor del campo path
     * @param string $path
     */
    public function setFecsis($fecsis)
    {
        $this->fecsis = $fecsis;
    }

    /**
     * Metodo para establecer el valor del campo ftpserver
     * @param string $ftpserver
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    /**
     * Devuelve el valor del campo codapl
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo email
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Devuelve el valor del campo clave
     * @return string
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Devuelve el valor del campo path
     * @return string
     */
    public function getFecsis()
    {
        return $this->fecsis;
    }

    /**
     * Devuelve el valor del campo ftpserver
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }
}
