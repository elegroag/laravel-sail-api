<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio20 extends ModelBase
{


    protected $table = 'mercurio20';
    public $timestamps = false;
    protected $primaryKey = 'log';

    protected $fillable = [
        'log',
        'tipo',
        'coddoc',
        'documento',
        'ip',
        'fecha',
        'hora',
        'accion',
        'nota',
    ];


    /**
     * Metodo para establecer el valor del campo log
     * @param integer $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Metodo para establecer el valor del campo tipo
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     * @param string $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo documento
     * @param string $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * Metodo para establecer el valor del campo ip
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Metodo para establecer el valor del campo hora
     * @param string $hora
     */
    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    /**
     * Metodo para establecer el valor del campo accion
     * @param string $accion
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
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
     * Devuelve el valor del campo log
     * @return integer
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Devuelve el valor del campo tipo
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo coddoc
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo documento
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Devuelve el valor del campo ip
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }


    public function getFecha()
    {
        return Carbon::parse($this->fecha);
    }

    /**
     * Devuelve el valor del campo hora
     * @return string
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Devuelve el valor del campo accion
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Devuelve el valor del campo nota
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }
}
