<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio52 extends ModelBase
{
    protected $table = 'mercurio52';
    public $timestamps = false;
    protected $primaryKey = 'codmen';

    protected $fillable = [
        'codmen',
        'detalle',
        'codare',
        'url',
        'tipo',
        'estado',
    ];

    // Setters
    public function setCodmen($codmen)
    {
        $this->codmen = $codmen;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setCodare($codare)
    {
        $this->codare = $codare;
    }

    public function setUrl($url)
    {
        $this->url = $url;
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
    public function getCodmen()
    {
        return $this->codmen;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getCodare()
    {
        return $this->codare;
    }

    public function getCodareDetalle()
    {
        $area = $this->area();
        return $area ? $area->getDetalle() : "";
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getTipoArray()
    {
        return ["T" => "TRABAJADOR", "E" => "EMPRESA", "C" => "CONYUGE", "B" => "BENEFICIARIO"];
    }

    public function getTipoDetalle()
    {
        $tipos = $this->getTipoArray();
        return $tipos[$this->tipo] ?? "";
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return ["A" => "ACTIVO", "I" => "INACTIVO"];
    }

    public function getEstadoDetalle()
    {
        $estados = $this->getEstadoArray();
        return $estados[$this->estado] ?? "";
    }

    // Relación Eloquent
    public function area()
    {
        return $this->belongsTo(Mercurio55::class, 'codare', 'codare');
    }
}
