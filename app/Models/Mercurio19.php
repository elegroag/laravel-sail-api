<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio19 extends ModelBase
{

    protected $table = 'mercurio19';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'codigo',
        'codver',
        'respuesta',
        'inicio',
        'intentos',
        'token',
    ];

    public function __construct(
        $tipo = null,
        $coddoc = null,
        $documento = null,
        $codigo = null,
        $respuesta = null,
        $intentos = null,
        $inicio = null,
        $token = null,
        $codver = null
    ) {
        parent::__construct();

        $this->tipo = $tipo;
        $this->coddoc = $coddoc;
        $this->documento = $documento;
        $this->codigo = $codigo;
        $this->respuesta = $respuesta;
        $this->inicio = $inicio;
        $this->intentos = $intentos;
        $this->token = $token;
        $this->codver = $codver;
    }

    public function setCodver($codver)
    {
        $this->codver = $codver;
    }

    public function getCodver()
    {
        return $this->codver;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setIntentos($intentos)
    {
        $this->intentos = $intentos;
    }

    public function getIntentos()
    {
        return $this->intentos;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    public function getInicio()
    {
        return $this->inicio;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getRespuesta()
    {
        return $this->respuesta;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }
}
