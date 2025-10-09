<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Sat14 extends ModelBase
{
    protected $table = 'sat14';

    public $timestamps = false;

    protected $primaryKey = 'numtraccf';

    protected $fillable = [
        'numtraccf',
        'numtrasat',
        'tipdocemp',
        'numdocemp',
        'sersat',
        'fecret',
        'cauret',
        'resultado',
        'mensaje',
        'codigo',
    ];

    // Setters
    public function setNumtraccf($numtraccf)
    {
        $this->numtraccf = $numtraccf;
    }

    public function setNumtrasat($numtrasat)
    {
        $this->numtrasat = $numtrasat;
    }

    public function setTipdocemp($tipdocemp)
    {
        $this->tipdocemp = $tipdocemp;
    }

    public function setNumdocemp($numdocemp)
    {
        $this->numdocemp = $numdocemp;
    }

    public function setSersat($sersat)
    {
        $this->sersat = $sersat;
    }

    public function setFecret($fecret)
    {
        $this->fecret = $fecret;
    }

    public function setCauret($cauret)
    {
        $this->cauret = $cauret;
    }

    public function setResultado($resultado)
    {
        $this->resultado = $resultado;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    // Getters
    public function getNumtraccf()
    {
        return $this->numtraccf;
    }

    public function getNumtrasat()
    {
        return $this->numtrasat;
    }

    public function getTipdocemp()
    {
        return $this->tipdocemp;
    }

    public function getNumdocemp()
    {
        return $this->numdocemp;
    }

    public function getSersat()
    {
        return $this->sersat;
    }

    public function getFecret()
    {
        return $this->fecret;
    }

    public function getCauret()
    {
        return $this->cauret;
    }

    public function getResultado()
    {
        return $this->resultado;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }
}
