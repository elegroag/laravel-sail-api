<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio33 extends ModelBase
{
    protected $table = 'mercurio33';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'campo',
        'antval',
        'valor',
        'estado',
        'motivo',
        'fecest',
        'usuario',
        'actualizacion',
        'log',
    ];

    public function __construct(?array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getEstadoDetalle()
    {
        $return = '';
        if ($this->estado == 'T') {
            $return = 'TEMPORAL';
        }
        if ($this->estado == 'D') {
            $return = 'DEVUELTO';
        }
        if ($this->estado == 'A') {
            $return = 'APROBADO';
        }
        if ($this->estado == 'X') {
            $return = 'RECHAZADO';
        }
        if ($this->estado == 'P') {
            $return = 'PENDIENTE';
        }

        return $return;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCampo($campo)
    {
        $this->campo = $campo;
    }

    public function setAntval($antval)
    {
        $this->antval = $antval;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setActualizacion($actualizacion)
    {
        $this->actualizacion = $actualizacion;
    }

    public function getCampo()
    {
        return $this->campo;
    }

    public function getAntval()
    {
        return $this->antval;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function getFecest()
    {
        return $this->fecest;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }
}
