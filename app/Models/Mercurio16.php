<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;

class Mercurio16 extends ModelBase
{
    use ValidateWithRules;

    protected $table = 'mercurio16';
    protected $primaryKey = 'id';

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'documento',
        'fecha',
        'firma',
        'coddoc',
        'keyprivate',
        'keypublic'
    ];

    protected function rules()
    {
        return [
            'documento' => 'required|numeric|min:5',
            'coddoc' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio16')->where(function ($query) {
                    if ($this->exists) {
                        $query->where('id', '!=', $this->id);
                    }
                    return $query->where('documento', $this->documento)
                        ->where('coddoc', $this->coddoc);
                }),
            ],
        ];
    }

    public function setKeypublic($keypublic)
    {
        $this->keypublic = $keypublic;
    }

    public function getKeypublic()
    {
        return $this->keypublic;
    }

    public function setKeyprivate($keyprivate)
    {
        $this->keyprivate = $keyprivate;
    }

    public function getKeyprivate()
    {
        return $this->keyprivate;
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

    public function setFirma($firma)
    {
        $this->firma = $firma;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getFirma()
    {
        return $this->firma;
    }

    public function getFecha()
    {
        return $this->fecha;
    }
}
