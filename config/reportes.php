<?php

use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;

return [
    'oportunidad_umbral_dias' => 3,

    'oportunidad_tipos' => [
        1 => [
            'model' => Mercurio31::class,
            'label' => 'TRABAJADOR',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        2 => [
            'model' => Mercurio30::class,
            'label' => 'EMPRESA',
            'doc_field' => 'nit',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        3 => [
            'model' => Mercurio32::class,
            'label' => 'CONYUGE',
            'doc_field' => 'cedcon',
            'afiliacion_field' => 'fecapr',
            'titular_field' => 'cedtra',
        ],
        4 => [
            'model' => Mercurio34::class,
            'label' => 'BENEFICIARIO',
            'doc_field' => 'numdoc',
            'afiliacion_field' => 'fecapr',
            'titular_field' => 'cedtra',
        ],
        9 => [
            'model' => Mercurio36::class,
            'label' => 'PENSIONADO',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        10 => [
            'model' => Mercurio38::class,
            'label' => 'FACULTATIVO',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        11 => [
            'model' => Mercurio39::class,
            'label' => 'INDEPENDIENTE',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
    ],
];
