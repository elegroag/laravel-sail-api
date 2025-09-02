<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio01 extends ModelBase
{

    protected $table = 'mercurio01';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'codapl',
        'email',
        'clave',
        'path',
    ];

    /**
     * Metodo para establecer el valor del campo codapl
     * @param string $codapl
     */
    public function setCodapl($codapl)
    {
        $this->codapl = $codapl;
    }

    /**
     * Metodo para establecer el valor del campo email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Metodo para establecer el valor del campo clave
     * @param string $clave
     */
    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    /**
     * Metodo para establecer el valor del campo path
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    /**
     * Devuelve el valor del campo codapl
     * @return string
     */
    public function getCodapl()
    {
        return $this->codapl;
    }

    /**
     * Devuelve el valor del campo email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Devuelve el valor del campo clave
     * @return string
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Devuelve el valor del campo path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
