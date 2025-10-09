<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio65 extends ModelBase
{
    protected $table = 'mercurio65';

    public $timestamps = false;

    protected $primaryKey = 'codsed';

    protected $fillable = [
        'codsed',
        'nit',
        'razsoc',
        'direccion',
        'email',
        'celular',
        'codcla',
        'detalle',
        'archivo',
        'lat',
        'log',
        'estado',
    ];

    // Setters
    public function setCodsed($codsed)
    {
        $this->codsed = $codsed;
    }

    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    public function setRazsoc($razsoc)
    {
        $this->razsoc = $razsoc;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function setCodcla($codcla)
    {
        $this->codcla = $codcla;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Getters
    public function getCodsed()
    {
        return $this->codsed;
    }

    public function getNit()
    {
        return $this->nit;
    }

    public function getRazsoc()
    {
        return $this->razsoc;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getCodcla()
    {
        return $this->codcla;
    }

    public function getCodclaDetalle()
    {
        $clasificacion = $this->clasificacion();

        return $clasificacion ? $clasificacion->getDetalle() : '';
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLog()
    {
        return $this->log;
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
    public function clasificacion()
    {
        return $this->belongsTo(Mercurio67::class, 'codcla', 'codcla');
    }
}
