<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Illuminate\Validation\Rule;

class Xml4b001 extends ModelBase
{
    protected $table = 'xml4b001';
    public $timestamps = false;
    protected $primaryKey = 'tipsec';

    protected $fillable = [
        'tipsec',
        'nombre',
        'tipemp',
    ];

    protected function rules()
    {
        return [
            'tipemp' => [
                'required',
                Rule::in(['O', 'P', 'M', 'N']),
            ],
        ];
    }

    // Setters
    public function setTipsec($tipsec)
    {
        $this->tipsec = $tipsec;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setTipemp($tipemp)
    {
        $this->tipemp = $tipemp;
    }

    // Getters
    public function getTipsec()
    {
        return $this->tipsec;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getTipemp()
    {
        return $this->tipemp;
    }

    public function getTipempArray()
    {
        return [
            'O' => 'OFICIAL',
            'P' => 'PRIVADA',
            'M' => 'MIXTA',
            'N' => 'NO APLICA'
        ];
    }

    public function getTipempDetalle()
    {
        $tipos = $this->getTipempArray();
        return $tipos[$this->tipemp] ?? '';
    }
}
