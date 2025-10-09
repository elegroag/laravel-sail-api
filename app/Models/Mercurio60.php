<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio60 extends ModelBase
{
    protected $table = 'mercurio60';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'id',
        'codinf',
        'codser',
        'numero',
        'tipo',
        'documento',
        'coddoc',
        'codcat',
        'valtot',
        'fecsis',
        'hora',
        'tipmov',
        'online',
        'consumo',
        'feccon',
        'punuti',
        'puntos',
        'estado',
    ];

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCodinf($codinf)
    {
        $this->codinf = $codinf;
    }

    public function setCodser($codser)
    {
        $this->codser = $codser;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setCodcat($codcat)
    {
        $this->codcat = $codcat;
    }

    public function setValtot($valtot)
    {
        $this->valtot = $valtot;
    }

    public function setFecsis($fecsis)
    {
        $this->fecsis = $fecsis;
    }

    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    public function setTipmov($tipmov)
    {
        $this->tipmov = $tipmov;
    }

    public function setOnline($online)
    {
        $this->online = $online;
    }

    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;
    }

    public function setFeccon($feccon)
    {
        $this->feccon = $feccon;
    }

    public function setPunuti($punuti)
    {
        $this->punuti = $punuti;
    }

    public function setPuntos($puntos)
    {
        $this->puntos = $puntos;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getCodinf()
    {
        return $this->codinf;
    }

    public function getCodser()
    {
        return $this->codser;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getCodcat()
    {
        return $this->codcat;
    }

    public function getValtot()
    {
        return $this->valtot;
    }

    public function getFecsis()
    {
        return $this->fecsis;
    }

    public function getHora()
    {
        return $this->hora;
    }

    public function getTipmov()
    {
        return $this->tipmov;
    }

    public function getOnline()
    {
        return $this->online;
    }

    public function getConsumo()
    {
        return $this->consumo;
    }

    public function getFeccon()
    {
        return $this->feccon;
    }

    public function getPunuti()
    {
        return $this->punuti;
    }

    public function getPuntos()
    {
        return $this->puntos;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return ['A' => 'ACTIVO', 'P' => 'PROCESADO'];
    }

    public function getEstadoDetalle()
    {
        $estados = $this->getEstadoArray();

        return $estados[$this->estado] ?? '';
    }
}
