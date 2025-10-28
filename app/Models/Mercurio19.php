<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio19 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio19';

    public $timestamps = false;

    protected $primaryKey = ['documento', 'coddoc', 'tipo'];

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

    protected function rules()
    {
        return [
            'documento' => 'required|numeric|min:5',
            'coddoc' => 'required|numeric|min:1',
            'tipo' => 'required|string|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio19')->where(function ($query) {
                    return $query->where('documento', $this->documento)
                        ->where('coddoc', $this->coddoc)
                        ->where('tipo', $this->tipo);
                }),
            ],
        ];
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
