<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio59 extends ModelBase
{
    protected $table = 'mercurio59';

    public $timestamps = false;

    protected $primaryKey = 'codinf';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codinf',
        'codser',
        'numero',
        'archivo',
        'nota',
        'email',
        'precan',
        'autser',
        'consumo',
        'estado',
    ];

    // Setters
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

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPrecan($precan)
    {
        $this->precan = $precan;
    }

    public function setAutser($autser)
    {
        $this->autser = $autser;
    }

    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    // Getters
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

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getNota()
    {
        return $this->nota;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPrecan()
    {
        return $this->precan;
    }

    public function getPrecanArray()
    {
        return ['S' => 'SI', 'N' => 'NO'];
    }

    public function getPrecanDetalle()
    {
        $precan = $this->getPrecanArray();

        return $precan[$this->precan] ?? '';
    }

    public function getAutser()
    {
        return $this->autser;
    }

    public function getAutserArray()
    {
        return ['S' => 'SI', 'N' => 'NO'];
    }

    public function getAutserDetalle()
    {
        $autser = $this->getAutserArray();

        return $autser[$this->autser] ?? '';
    }

    public function getConsumo()
    {
        return $this->consumo;
    }

    public function getConsumoArray()
    {
        return ['S' => 'SI', 'N' => 'NO'];
    }

    public function getConsumoDetalle()
    {
        $consumo = $this->getConsumoArray();

        return $consumo[$this->consumo] ?? '';
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
