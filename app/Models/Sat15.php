<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Sat15 extends ModelBase
{
    protected $table = 'sat15';

    public $timestamps = false;

    protected $primaryKey = 'numtraccf';

    protected $fillable = [
        'numtraccf',
        'tipdocemp',
        'numdocemp',
        'sersat',
        'estpag',
        'resultado',
        'mensaje',
        'codigo',
    ];

    // Setters
    public function setNumtraccf($numtraccf)
    {
        $this->numtraccf = $numtraccf;
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

    public function setEstpag($estpag)
    {
        $this->estpag = $estpag;
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

    public function getEstpag()
    {
        return $this->estpag;
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
