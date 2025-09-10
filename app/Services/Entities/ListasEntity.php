<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class ListasEntity
{
    use ValidatorTrait;

    protected $fillable = array(
        'nit',
        'codlis',
        'detalle',
        'direccion',
        'telefono',
        'fax',
        'coddiv',
        'nomcon',
        'email'
    );

    protected function getRules()
    {
        return array(
            'nit' => array('type' => 'string', 'max' => 18, 'required' => true),
            'codlis' => array('type' => 'string', 'max' => 3, 'required' => true),
            'detalle' => array('type' => 'string', 'max' => 140, 'required' => true),
            'direccion' => array('type' => 'string', 'max' => 120, 'required' => false),
            'telefono' => array('type' => 'string', 'max' => 20, 'required' => false),
            'fax' => array('type' => 'string', 'max' => 13, 'required' => false, 'is_null' => true),
            'coddiv' => array('type' => 'string', 'max' => 5, 'required' => true, 'is_null' => false),
            'nomcon' => array('type' => 'string', 'max' => 40, 'required' => false),
            'email' => array('type' => 'email', 'max' => 60, 'required' => false)
        );
    }
}
