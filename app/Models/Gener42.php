<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener42 extends ModelBase
{
    protected $table = 'gener42';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'usuario',
        'permiso',
    ];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setPermiso($permiso)
    {
        $this->permiso = $permiso;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }
}
