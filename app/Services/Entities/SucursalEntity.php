<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class SucursalEntity
{
    use ValidatorTrait;

    protected $fillable = [
        'nit',
        'codsuc',
        'detalle',
        'subpla',
        'direccion',
        'codciu',
        'telefono',
        'fax',
        'codzon',
        'nomcon',
        'email',
        'calsuc',
        'codact',
        'codind',
        'traapo',
        'valapo',
        'actapr',
        'fecafi',
        'feccam',
        'estado',
        'resest',
        'codest',
        'fecest',
        'tottra',
        'totapo',
        'totcon',
        'tothij',
        'tother',
        'totpad',
        'tietra',
        'tratot',
        'correo',
        'observacion',
        'pagadora',
    ];

    protected function getRules()
    {
        return [
            'nit' => ['type' => 'string', 'max' => 14, 'required' => true],
            'codsuc' => ['type' => 'string', 'max' => 3, 'required' => true],
            'detalle' => ['type' => 'string', 'max' => 180, 'required' => true],
            'subpla' => ['type' => 'string', 'max' => 20, 'required' => false],
            'direccion' => ['type' => 'string', 'max' => 120, 'required' => true],
            'codciu' => ['type' => 'string', 'max' => 5, 'required' => false],
            'telefono' => ['type' => 'string', 'max' => 25, 'required' => false],
            'fax' => ['type' => 'string', 'max' => 13, 'required' => false, 'is_null' => true],
            'codzon' => ['type' => 'string', 'max' => 9, 'required' => true],
            'nomcon' => ['type' => 'string', 'max' => 70, 'required' => false],
            'email' => ['type' => 'string', 'max' => 100, 'required' => false],
            'calsuc' => ['type' => 'enum', 'values' => ['E', 'I', 'F', 'P', 'S', 'O', 'A', 'M', 'D', 'N', 'PA'], 'required' => true],
            'codact' => ['type' => 'string', 'max' => 6, 'required' => false],
            'codind' => ['type' => 'string', 'max' => 2, 'required' => false],
            'traapo' => ['type' => 'integer', 'required' => false],
            'valapo' => ['type' => 'numeric', 'required' => false],
            'actapr' => ['type' => 'string', 'max' => 50, 'required' => false],
            'fecafi' => ['type' => 'date', 'required' => false],
            'feccam' => ['type' => 'date', 'required' => false],
            'estado' => ['type' => 'enum', 'values' => ['A', 'I', 'S'], 'required' => true],
            'resest' => ['type' => 'string', 'max' => 50, 'required' => false, 'is_null' => true],
            'codest' => ['type' => 'string', 'max' => 2, 'required' => false, 'is_null' => true],
            'fecest' => ['type' => 'date', 'required' => false],
            'tottra' => ['type' => 'integer', 'required' => true],
            'totapo' => ['type' => 'integer', 'required' => true],
            'totcon' => ['type' => 'integer', 'required' => true],
            'tothij' => ['type' => 'integer', 'required' => true],
            'tother' => ['type' => 'integer', 'required' => true],
            'totpad' => ['type' => 'integer', 'required' => true],
            'tietra' => ['type' => 'enum', 'values' => ['S', 'N'], 'required' => true],
            'tratot' => ['type' => 'integer', 'required' => true],
            'correo' => ['type' => 'enum', 'values' => ['S', 'N'], 'required' => true],
            'observacion' => ['type' => 'string', 'max' => 255, 'required' => false],
            'pagadora' => ['type' => 'enum', 'values' => ['S', 'N'], 'required' => false, 'default' => 'N'],
        ];
    }
}
