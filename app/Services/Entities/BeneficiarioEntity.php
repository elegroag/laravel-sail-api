<?php

namespace App\Services\Entities;

use App\Services\Validator\ValidatorTrait;

class BeneficiarioEntity
{
    use ValidatorTrait;

    protected $fillable = [
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
        'cedacu',
    ];

    protected function getRules()
    {
        return [
            'codben' => ['type' => 'integer', 'is_null' => false],
            'documento' => ['type' => 'string', 'max' => 20, 'is_null' => false],
            'coddoc' => ['type' => 'string', 'max' => 2, 'is_null' => false],
            'priape' => ['type' => 'string', 'max' => 20, 'is_null' => true],
            'segape' => ['type' => 'string', 'max' => 20, 'is_null' => true],
            'prinom' => ['type' => 'string', 'max' => 30, 'is_null' => false],
            'segnom' => ['type' => 'string', 'max' => 20, 'is_null' => true],
            'parent' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'huerfano' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'tiphij' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'captra' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'tipdis' => ['type' => 'string', 'max' => 2, 'is_null' => true],
            'sexo' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'fecnac' => ['type' => 'date', 'is_null' => false],
            'ciunac' => ['type' => 'string', 'max' => 5, 'is_null' => true],
            'calendario' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'giro' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'codgir' => ['type' => 'string', 'max' => 2, 'is_null' => true, 'default' => 'NU'],
            'estado' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'codest' => ['type' => 'string', 'max' => 2, 'is_null' => true],
            'fecest' => ['type' => 'date', 'is_null' => true],
            'feccon' => ['type' => 'date', 'is_null' => true],
            'giass' => ['type' => 'string', 'max' => 2, 'is_null' => true],
            'usuario' => ['type' => 'string', 'max' => 10, 'is_null' => true],
            'cedtra' => ['type' => 'string', 'max' => 18, 'is_null' => false],
            'cedcon' => ['type' => 'string', 'max' => 20, 'is_null' => true],
            'numhij' => ['type' => 'numeric', 'is_null' => false, 'max' => 2],
            'pago' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'fecpre' => ['type' => 'date', 'is_null' => false],
            'fecafi' => ['type' => 'date', 'is_null' => false],
            'fecsis' => ['type' => 'date', 'is_null' => false],
            'ruaf' => ['type' => 'string', 'max' => 1, 'is_null' => false],
            'numrua' => ['type' => 'integer', 'is_null' => true],
            'fosfec' => ['type' => 'string', 'max' => 1, 'is_null' => true],
            'fecrua' => ['type' => 'date', 'is_null' => true],
            'cedacu' => ['type' => 'string', 'max' => 20, 'is_null' => true],
        ];
    }
}
