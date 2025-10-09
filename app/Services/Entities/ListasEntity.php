<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class ListasEntity
{
    use ValidatorTrait;

    protected $fillable = [
        'nit',
        'codlis',
        'detalle',
        'direccion',
        'telefono',
        'fax',
        'coddiv',
        'nomcon',
        'email',
    ];

    protected function getRules()
    {
        return [
            'nit' => ['type' => 'string', 'max' => 18, 'required' => true],
            'codlis' => ['type' => 'string', 'max' => 3, 'required' => true],
            'detalle' => ['type' => 'string', 'max' => 140, 'required' => true],
            'direccion' => ['type' => 'string', 'max' => 120, 'required' => false],
            'telefono' => ['type' => 'string', 'max' => 20, 'required' => false],
            'fax' => ['type' => 'string', 'max' => 13, 'required' => false, 'is_null' => true],
            'coddiv' => ['type' => 'string', 'max' => 5, 'required' => true, 'is_null' => false],
            'nomcon' => ['type' => 'string', 'max' => 40, 'required' => false],
            'email' => ['type' => 'email', 'max' => 60, 'required' => false],
        ];
    }
}
