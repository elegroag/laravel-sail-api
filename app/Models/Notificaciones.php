<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Services\Request;

class Notificaciones extends ModelBase
{

    protected $table = 'notificaciones';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'titulo',
        'descri',
        'user',
        'estado',
        'progre',
        'result',
        'dia',
        'hora',
    ];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setDescri($descri)
    {
        $this->descri = $descri;
    }

    public function getDescri()
    {
        return $this->descri;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setProgre($progre)
    {
        $this->progre = $progre;
    }

    public function getProgre()
    {
        return $this->progre;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setDia($dia)
    {
        $this->dia = $dia;
    }

    public function getDia()
    {
        return $this->dia;
    }

    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    public function getHora()
    {
        return $this->hora;
    }
}
