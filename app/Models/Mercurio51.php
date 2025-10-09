<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio51 extends ModelBase
{
    protected $table = 'mercurio51';

    public $timestamps = false;

    protected $primaryKey = 'codcat';

    public $incrementing = false;

    protected $fillable = [
        'codcat',
        'detalle',
        'tipo',
        'estado',
    ];

    // Setters
    public function setCodcat($codcat)
    {
        $this->codcat = $codcat;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Getters
    public function getCodcat()
    {
        return $this->codcat;
    }

    public function getCodcatDetalle()
    {
        $foreing = $this->find($this->codcat);
        if ($foreing) {
            return $foreing->getDetalle();
        }

        return '';
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getTipoArray()
    {
        return ['T' => 'TRABAJADOR', 'E' => 'EMPRESA', 'C' => 'CONYUGE', 'B' => 'BENEFICIARIO'];
    }

    public function getTipoDetalle()
    {
        $tipos = $this->getTipoArray();

        return $tipos[$this->tipo] ?? '';
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return ['A' => 'ACTIVO', 'I' => 'INACTIVO'];
    }

    public function getEstadoDetalle()
    {
        $estados = $this->getEstadoArray();

        return $estados[$this->estado] ?? '';
    }
}
