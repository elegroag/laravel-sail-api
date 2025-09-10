<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class SucursalEntity
{
    use ValidatorTrait;

    protected $fillable = array(
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
        'pagadora'
    );

    protected function getRules()
    {
        return array(
            'nit' => array('type' => 'string', 'max' => 14, 'required' => true),
            'codsuc' => array('type' => 'string', 'max' => 3, 'required' => true),
            'detalle' => array('type' => 'string', 'max' => 180, 'required' => true),
            'subpla' => array('type' => 'string', 'max' => 20, 'required' => false),
            'direccion' => array('type' => 'string', 'max' => 120, 'required' => true),
            'codciu' => array('type' => 'string', 'max' => 5, 'required' => false),
            'telefono' => array('type' => 'string', 'max' => 25, 'required' => false),
            'fax' => array('type' => 'string', 'max' => 13, 'required' => false, 'is_null' => true),
            'codzon' => array('type' => 'string', 'max' => 9, 'required' => true),
            'nomcon' => array('type' => 'string', 'max' => 70, 'required' => false),
            'email' => array('type' => 'string', 'max' => 100, 'required' => false),
            'calsuc' => array('type' => 'enum', 'values' => ['E', 'I', 'F', 'P', 'S', 'O', 'A', 'M', 'D', 'N', 'PA'], 'required' => true),
            'codact' => array('type' => 'string', 'max' => 6, 'required' => false),
            'codind' => array('type' => 'string', 'max' => 2, 'required' => false),
            'traapo' => array('type' => 'integer', 'required' => false),
            'valapo' => array('type' => 'numeric', 'required' => false),
            'actapr' => array('type' => 'string', 'max' => 50, 'required' => false),
            'fecafi' => array('type' => 'date', 'required' => false),
            'feccam' => array('type' => 'date', 'required' => false),
            'estado' => array('type' => 'enum', 'values' => ['A', 'I', 'S'], 'required' => true),
            'resest' => array('type' => 'string', 'max' => 50, 'required' => false, 'is_null' => true),
            'codest' => array('type' => 'string', 'max' => 2, 'required' => false, 'is_null' => true),
            'fecest' => array('type' => 'date', 'required' => false),
            'tottra' => array('type' => 'integer', 'required' => true),
            'totapo' => array('type' => 'integer', 'required' => true),
            'totcon' => array('type' => 'integer', 'required' => true),
            'tothij' => array('type' => 'integer', 'required' => true),
            'tother' => array('type' => 'integer', 'required' => true),
            'totpad' => array('type' => 'integer', 'required' => true),
            'tietra' => array('type' => 'enum', 'values' => ['S', 'N'], 'required' => true),
            'tratot' => array('type' => 'integer', 'required' => true),
            'correo' => array('type' => 'enum', 'values' => ['S', 'N'], 'required' => true),
            'observacion' => array('type' => 'string', 'max' => 255, 'required' => false),
            'pagadora' => array('type' => 'enum', 'values' => ['S', 'N'], 'required' => false, 'default' => 'N')
        );
    }
}
