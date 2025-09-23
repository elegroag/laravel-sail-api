<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class RecepcionSat extends ModelBase
{
    protected $table = 'recepcionsat';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id',
        'contenido',
        'numero_transaccion',
        'fecha',
    ];

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setContenido($contenido)
    {
        $this->contenido = $contenido;
    }

    public function setNumeroTransaccion($numero_transaccion)
    {
        $this->numero_transaccion = $numero_transaccion;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getContenido()
    {
        return $this->contenido;
    }

    public function getNumeroTransaccion()
    {
        return $this->numero_transaccion;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    // Métodos auxiliares
    public function getContenidoJson()
    {
        $contenido = $this->getContenido();
        if ($contenido) {
            return json_decode($contenido, true);
        }
        return null;
    }

    public function getResultado()
    {
        $json = $this->getContenidoJson();
        return $json['resultado'] ?? null;
    }

    public function getMensaje()
    {
        $json = $this->getContenidoJson();
        return $json['mensaje'] ?? null;
    }

    public function getCodigo()
    {
        $json = $this->getContenidoJson();
        return $json['codigo'] ?? null;
    }
}
