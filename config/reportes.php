<?php

use App\Models\AuditoriaMercurio30;
use App\Models\AuditoriaMercurio31;
use App\Models\AuditoriaMercurio32;
use App\Models\AuditoriaMercurio34;
use App\Models\AuditoriaMercurio36;
use App\Models\AuditoriaMercurio38;
use App\Models\AuditoriaMercurio39;
use App\Models\AuditoriaMercurio41;
use App\Models\AuditoriaMercurio45;
use App\Models\AuditoriaMercurio47;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio41;
use App\Models\Mercurio45;
use App\Models\Mercurio47;

return [
    'oportunidad_umbral_dias' => 3,

    /**
     * Mapa tipopc => modelo vivo + modelo de auditoría (fallback tras DELETE).
     *
     * @var array<int|string, array{model: class-string, audit_model: class-string}>
     */
    'solicitud_auditoria' => [
        1 => ['model' => Mercurio31::class, 'audit_model' => AuditoriaMercurio31::class],
        2 => ['model' => Mercurio30::class, 'audit_model' => AuditoriaMercurio30::class],
        3 => ['model' => Mercurio32::class, 'audit_model' => AuditoriaMercurio32::class],
        4 => ['model' => Mercurio34::class, 'audit_model' => AuditoriaMercurio34::class],
        5 => ['model' => Mercurio47::class, 'audit_model' => AuditoriaMercurio47::class],
        6 => ['model' => Mercurio47::class, 'audit_model' => AuditoriaMercurio47::class],
        8 => ['model' => Mercurio45::class, 'audit_model' => AuditoriaMercurio45::class],
        9 => ['model' => Mercurio36::class, 'audit_model' => AuditoriaMercurio36::class],
        10 => ['model' => Mercurio38::class, 'audit_model' => AuditoriaMercurio38::class],
        11 => ['model' => Mercurio39::class, 'audit_model' => AuditoriaMercurio39::class],
        13 => ['model' => Mercurio41::class, 'audit_model' => AuditoriaMercurio41::class],
    ],

    'oportunidad_tipos' => [
        1 => [
            'model' => Mercurio31::class,
            'audit_model' => AuditoriaMercurio31::class,
            'label' => 'TRABAJADOR',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        2 => [
            'model' => Mercurio30::class,
            'audit_model' => AuditoriaMercurio30::class,
            'label' => 'EMPRESA',
            'doc_field' => 'nit',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        3 => [
            'model' => Mercurio32::class,
            'audit_model' => AuditoriaMercurio32::class,
            'label' => 'CONYUGE',
            'doc_field' => 'cedcon',
            'afiliacion_field' => 'fecapr',
            'titular_field' => 'cedtra',
        ],
        4 => [
            'model' => Mercurio34::class,
            'audit_model' => AuditoriaMercurio34::class,
            'label' => 'BENEFICIARIO',
            'doc_field' => 'numdoc',
            'afiliacion_field' => 'fecapr',
            'titular_field' => 'cedtra',
        ],
        9 => [
            'model' => Mercurio36::class,
            'audit_model' => AuditoriaMercurio36::class,
            'label' => 'PENSIONADO',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        10 => [
            'model' => Mercurio38::class,
            'audit_model' => AuditoriaMercurio38::class,
            'label' => 'FACULTATIVO',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
        11 => [
            'model' => Mercurio39::class,
            'audit_model' => AuditoriaMercurio39::class,
            'label' => 'INDEPENDIENTE',
            'doc_field' => 'cedtra',
            'afiliacion_field' => 'fecapr',
            'titular_field' => null,
        ],
    ],
];
