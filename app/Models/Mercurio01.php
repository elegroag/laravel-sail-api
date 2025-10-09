<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio01 extends ModelBase
{
    protected $table = 'mercurio01';

    public $timestamps = false;

    // PK es CHAR(2) 'codapl' (no autoincremental)
    protected $primaryKey = 'codapl';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codapl',
        'email',
        'clave',
        'path',
        'ftpserver',
        'pathserver',
        'userserver',
        'passserver',
    ];

    /**
     * Metodo para establecer el valor del campo codapl
     *
     * @param  string  $codapl
     */
    public function setCodapl($codapl)
    {
        $this->codapl = $codapl;
    }

    /**
     * Metodo para establecer el valor del campo email
     *
     * @param  string  $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Metodo para establecer el valor del campo clave
     *
     * @param  string  $clave
     */
    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    /**
     * Metodo para establecer el valor del campo path
     *
     * @param  string  $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Metodo para establecer el valor del campo ftpserver
     *
     * @param  string  $ftpserver
     */
    public function setFtpserver($ftpserver)
    {
        $this->ftpserver = $ftpserver;
    }

    /**
     * Metodo para establecer el valor del campo pathserver
     *
     * @param  string  $pathserver
     */
    public function setPathserver($pathserver)
    {
        $this->pathserver = $pathserver;
    }

    /**
     * Metodo para establecer el valor del campo userserver
     *
     * @param  string  $userserver
     */
    public function setUserserver($userserver)
    {
        $this->userserver = $userserver;
    }

    /**
     * Metodo para establecer el valor del campo passserver
     *
     * @param  string  $passserver
     */
    public function setPassserver($passserver)
    {
        $this->passserver = $passserver;
    }

    /**
     * Devuelve el valor del campo codapl
     *
     * @return string
     */
    public function getCodapl()
    {
        return $this->codapl;
    }

    /**
     * Devuelve el valor del campo email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Devuelve el valor del campo clave
     *
     * @return string
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Devuelve el valor del campo path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Devuelve el valor del campo ftpserver
     *
     * @return string
     */
    public function getFtpserver()
    {
        return $this->ftpserver;
    }

    /**
     * Devuelve el valor del campo pathserver
     *
     * @return string
     */
    public function getPathserver()
    {
        return $this->pathserver;
    }

    /**
     * Devuelve el valor del campo userserver
     *
     * @return string
     */
    public function getUserserver()
    {
        return $this->userserver;
    }

    /**
     * Devuelve el valor del campo passserver
     *
     * @return string
     */
    public function getPassserver()
    {
        return $this->passserver;
    }
}
