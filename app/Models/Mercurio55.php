<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio55 extends ModelBase
{
    protected $table = 'mercurio55';

    public $timestamps = false;

    protected $primaryKey = 'codare';

    protected $fillable = [
        'codare',
        'detalle',
        'codcat',
        'tipo',
        'estado',
    ];

    // Setters
    public function setCodare($codare)
    {
        $this->codare = $codare;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setCodcat($codcat)
    {
        $this->codcat = $codcat;
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
    public function getCodare()
    {
        return $this->codare;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getCodcat()
    {
        return $this->codcat;
    }

    public function getCodcatDetalle()
    {
        $categoria = $this->categoria();

        return $categoria ? $categoria->getDetalle() : '';
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

    // Relación Eloquent
    public function categoria()
    {
        return $this->belongsTo(Mercurio51::class, 'codcat', 'codcat');
    }
}
