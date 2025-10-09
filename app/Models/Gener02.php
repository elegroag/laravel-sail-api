<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Illuminate\Support\Facades\DB;

class Gener02 extends ModelBase
{
    protected $table = 'gener02';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario',
        'nombre',
        'tipfun',
        'email',
        'login',
        'acceso',
        'criptada',
        'estado',
        'cedtra',
        'clave',
    ];

    public function getClave()
    {
        return $this->clave;
    }

    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    public function getAcceso()
    {
        return $this->acceso;
    }

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function getCriptada()
    {
        return $this->criptada;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setAcceso($acceso)
    {
        $this->acceso = $acceso;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function setCriptada($criptada)
    {
        $this->criptada = $criptada;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setTipfun($tipfun)
    {
        $this->tipfun = $tipfun;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getTipfun()
    {
        return $this->tipfun;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getTipfunLista()
    {
        // Obtiene lista de tipos de funcionario desde tabla gener21
        $rows = DB::table('gener21')->select('tipfun', 'detalle')->get();
        $data = [];
        foreach ($rows as $row) {
            $data[$row->tipfun] = $row->detalle;
        }

        return $data;
    }

    public function getTipfunDetalle($tipfun = '')
    {
        if ($tipfun !== '') {
            $this->tipfun = $tipfun;
        }
        $row = DB::table('gener21')
            ->select('detalle')
            ->where('tipfun', $this->tipfun)
            ->first();

        if (! $row) {
            return false;
        }

        return $row->detalle;
    }
}
