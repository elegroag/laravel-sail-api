<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class BeneficiarioEntity
{
    use ValidatorTrait;

    protected $fillable = array(
        'codben',
        'documento',
        'coddoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'parent',
        'huerfano',
        'tiphij',
        'captra',
        'tipdis',
        'sexo',
        'fecnac',
        'ciunac',
        'calendario',
        'giro',
        'codgir',
        'estado',
        'codest',
        'fecest',
        'feccon',
        'giass',
        'usuario',
        'cedtra',
        'cedcon',
        'numhij',
        'pago',
        'fecpre',
        'fecafi',
        'fecsis',
        'ruaf',
        'numrua',
        'fosfec',
        'fecrua',
        'cedacu'
    );

    protected function getRules()
    {
        return array(
            'codben' => array('type' => 'integer', 'is_null' => false),
            'documento' => array('type' => 'string', 'max' => 20, 'is_null' => false),
            'coddoc' => array('type' => 'string', 'max' => 2, 'is_null' => false),
            'priape' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'segape' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'prinom' => array('type' => 'string', 'max' => 30, 'is_null' => false),
            'segnom' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'parent' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'huerfano' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'tiphij' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'captra' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'tipdis' => array('type' => 'string', 'max' => 2, 'is_null' => true),
            'sexo' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'fecnac' => array('type' => 'date', 'is_null' => false),
            'ciunac' => array('type' => 'string', 'max' => 5, 'is_null' => true),
            'calendario' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'giro' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'codgir' => array('type' => 'string', 'max' => 2, 'is_null' => true, 'default' => 'NU'),
            'estado' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'codest' => array('type' => 'string', 'max' => 2, 'is_null' => true),
            'fecest' => array('type' => 'date', 'is_null' => true),
            'feccon' => array('type' => 'date', 'is_null' => true),
            'giass' => array('type' => 'string', 'max' => 2, 'is_null' => true),
            'usuario' => array('type' => 'string', 'max' => 10, 'is_null' => true),
            'cedtra' => array('type' => 'string', 'max' => 18, 'is_null' => false),
            'cedcon' => array('type' => 'string', 'max' => 20, 'is_null' => true),
            'numhij' => array('type' => 'numeric', 'is_null' => false, 'max' => 2),
            'pago' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'fecpre' => array('type' => 'date', 'is_null' => false),
            'fecafi' => array('type' => 'date', 'is_null' => false),
            'fecsis' => array('type' => 'date', 'is_null' => false),
            'ruaf' => array('type' => 'string', 'max' => 1, 'is_null' => false),
            'numrua' => array('type' => 'integer', 'is_null' => true),
            'fosfec' => array('type' => 'string', 'max' => 1, 'is_null' => true),
            'fecrua' => array('type' => 'date', 'is_null' => true),
            'cedacu' => array('type' => 'string', 'max' => 20, 'is_null' => true)
        );
    }
}
