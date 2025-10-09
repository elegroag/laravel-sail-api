<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio56 extends ModelBase
{
    protected $table = 'mercurio56';

    public $timestamps = false;

    protected $primaryKey = 'codinf';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codinf',
        'archivo',
        'email',
        'telefono',
        'nota',
        'estado',
    ];

    // Setters
    public function setCodinf($codinf)
    {
        $this->codinf = $codinf;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setNota($nota)
    {
        $this->nota = $nota;
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

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getNota()
    {
        return $this->nota;
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
