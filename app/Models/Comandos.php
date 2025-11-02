<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Comandos extends ModelBase
{
    protected $table = 'comandos';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'fecha_runner',
        'hora_runner',
        'usuario',
        'progreso',
        'estado',
        'proceso',
        'linea_comando',
        'estructura',
        'parametros',
        'resultado',
    ];

    public function setResultado($resultado)
    {
        $this->resultado = $resultado;
    }

    public function getResultado()
    {
        return $this->resultado;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFechaRunner($fecha_runner)
    {
        $this->fecha_runner = $fecha_runner;
    }

    public function getFechaRunner()
    {
        return $this->fecha_runner;
    }

    public function setHoraRunner($hora_runner)
    {
        $this->hora_runner = $hora_runner;
    }

    public function getHoraRunner()
    {
        return $this->hora_runner;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setProgreso($progreso)
    {
        $this->progreso = $progreso;
    }

    public function getProgreso()
    {
        return $this->progreso;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setProceso($proceso)
    {
        $this->proceso = $proceso;
    }

    public function getProceso()
    {
        return $this->proceso;
    }

    public function setLineaComando($linea_comando)
    {
        $this->linea_comando = $linea_comando;
    }

    public function getLineaComando()
    {
        return $this->linea_comando;
    }

    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
    }

    public function getEstructura()
    {
        return $this->estructura;
    }

    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
    }

    public function getParametros()
    {
        return $this->parametros;
    }

    public function getEstadosArray()
    {
        return [
            'P' => 'Pendiente',
            'F' => 'Finalizado',
            'X' => 'Cancelado',
            'E' => 'Ejecución',
        ];
    }

    public function getEstadoDetalle($estado = '')
    {
        if (! empty($estado)) {
            $this->estado = $estado;
        }
        switch ($this->estado) {
            case 'P':
                return 'Pendiente';
                break;
            case 'F':
                return 'Finalizado';
                break;
            case 'C':
                return 'Cancelado';
                break;
            case 'E':
                return 'Ejecución';
                break;
        }
    }

    public function initialize()
    {
        $this->hasMany('id', 'comandos', 'estructura');
        $this->belongsTo('id', 'comandos', 'estructura');
    }
}
