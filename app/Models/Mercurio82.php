<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio82 extends ModelBase
{
    protected $table = 'mercurio82';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'numdoc',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'estado'
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNumdoc($numdoc)
    {
        $this->numdoc = $numdoc;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNumdoc()
    {
        return $this->numdoc;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return [
            "A" => "ACTIVO",
            "I" => "INACTIVO"
        ];
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if($this->estado == "A") $return = "ACTIVO";
        if($this->estado == "I") $return = "INACTIVO";
        return $return;
    }
}
