<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio03 extends ModelBase
{
    protected $table = 'mercurio03';
    public $timestamps = false;
    protected $primaryKey = 'codfir';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codfir',
        'nombre',
        'cargo',
        'archivo',
        'email',
    ];

    // Setters
    public function setCodfir($codfir)
    {
        $this->codfir = $codfir;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    // Getters
    public function getCodfir()
    {
        return $this->codfir;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
