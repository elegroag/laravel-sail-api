<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class ConyugeEntity
{
    use ValidatorTrait;

    protected $fillable = array(
        'cedtra',
        'cedcon',
        'fecafi',
        'recsub',
        'comper',
        'ruaf',
        'numrua',
        'fosfec',
        'fecrua',
        'coddoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'direccion',
        'telefono',
        'email',
        'codzon',
        'codcaj',
        'codocu',
        'nivedu',
        'captra',
        'salario',
        'tipsal',
        'fecsal',
        'tippag',
        'codcue',
        'ofides',
        'codgru',
        'codban',
        'numcue',
        'tipcue',
        'sexo',
        'estciv',
        'fecnac',
        'ciunac',
        'estado',
        'fecest',
        'numtar',
        'giass',
        'usuario',
        'fecact',
        'empresalab'
    );

    protected function getRules()
    {
        return array(
            'cedtra' => array('type' => 'string', 'max' => 18, 'is_null' => false),
            'cedcon' => array('type' => 'string', 'max' => 20, 'is_null' => false),
            'fecafi' => array('type' => 'date', 'is_null' => false),
            'recsub' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'comper' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'ruaf' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'numrua' => array('type' => 'integer', 'is_null' => true),
            'fosfec' => array('type' => 'string', 'max' => 1, 'is_null' => true),
            'fecrua' => array('type' => 'date', 'is_null' => true),
            'coddoc' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'priape' => array('type' => 'string', 'max' => 20, 'is_null' => false),
            'segape' => array('type' => 'string', 'max' => 30, 'is_null' => true),
            'prinom' => array('type' => 'string', 'max' => 30, 'is_null' => false),
            'segnom' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'direccion' => array('type' => 'string', 'max' => 40, 'is_null' => true),
            'telefono' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'email' => array('type' => 'string', 'max' => 50, 'is_null' => true),
            'codzon' => array('type' => 'string', 'max' => 9, 'is_null' => false),
            'codcaj' => array('type' => 'string', 'max' => 2, 'is_null' => true),
            'codocu' => array('type' => 'string', 'max' => 2, 'is_null' => true),
            'nivedu' => array('type' => 'string', 'max' => 2, 'is_null' => false),
            'captra' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'salario' => array('type' => 'integer', 'is_null' => false),
            'tipsal' => array('type' => 'string', 'max' => 1, 'is_null' => true),
            'fecsal' => array('type' => 'date', 'is_null' => true),
            'tippag' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'codcue' => array('type' => 'string', 'max' => 3, 'is_null' => true),
            'ofides' => array('type' => 'string', 'max' => 4, 'is_null' => true),
            'codgru' => array('type' => 'string', 'max' => 3, 'is_null' => true),
            'codban' => array('type' => 'string', 'max' => 5, 'is_null' => true),
            'numcue' => array('type' => 'string', 'max' => 17, 'is_null' => true),
            'tipcue' => array('type' => 'string', 'max' => 1, 'is_null' => true),
            'sexo' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'estciv' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'fecnac' => array('type' => 'date', 'is_null' => false),
            'ciunac' => array('type' => 'string', 'max' => 5, 'is_null' => true),
            'estado' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'fecest' => array('type' => 'date', 'is_null' => true),
            'numtar' => array('type' => 'string', 'max' => 15, 'is_null' => true),
            'giass' => array('type' => 'string', 'max' => 1, 'is_null' => true),
            'usuario' => array('type' => 'string', 'max' => 10, 'is_null' => true),
            'fecact' => array('type' => 'date', 'is_null' => true),
            'empresalab' => array('type' => 'string', 'max' => 100, 'is_null' => true)
        );
    }
}
