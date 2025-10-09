<?php

if (! function_exists('oficios_requeridos')) {

    function oficios_requeridos($tipafi)
    {
        switch ($tipafi) {
            case 'I':
                return __op_independiente();
                break;
            case 'E':
                return __op_empresa();
                break;
            case 'T':
                return __op_trabajador();
                break;
            case 'C':
                return __op_conyuge();
                break;
            case 'B':
                return __op_beneficiario();
                break;
            case 'O':
                return __op_pensionado();
                break;
            case 'F':
                return __op_facultativo();
                break;
            case 'U':
                return __op_actualizadatos();
                break;
            default:
                return false;
                break;
        }
    }

    function __op_actualizadatos()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'actualizadatos/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
        ];
    }

    function __op_independiente()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'independiente/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_empresario.pdf',
                'url' => 'independiente/cartaSolicitud',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_11.pdf',
                'url' => 'independiente/tratamientoDatos',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
        ];
    }

    function __op_empresa()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'empresa/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_empresario',
                'url' => 'empresa/cartaSolicitud',
                'auto_generar' => true,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'oficio_tratamiento_datos_personales.pdf',
                'url' => 'empresa/tratamientoDatos',
                'auto_generar' => true,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Relación trabajadores en nomina',
                'file' => 'oficio_tratamiento_datos_personales.pdf',
                'url' => 'empresa/tratamientoDatos',
                'auto_generar' => true,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
        ];
    }

    function __op_trabajador()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'independiente',
                'url' => 'trabajador/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_trabajador.pdf',
                'url' => 'trabajador',
                'auto_generar' => false,
                'firmas' => [
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_10.pdf',
                'url' => 'trabajador',
                'auto_generar' => false,
                'firmas' => [
                    'E' => 'Empleador',
                    'T' => 'Trabajador',
                ],
            ],
        ];
    }

    function __op_conyuge()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'conyuge/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_11.pdf',
                'url' => 'conyuge',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
        ];
    }

    function __op_beneficiario()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'beneficiario/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_empresario.pdf',
                'url' => 'beneficiario',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_10.pdf',
                'url' => 'beneficiario',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                    'E' => 'Empleador',
                ],
            ],
        ];
    }

    function __op_pensionado()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'pensionado/formulario',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_empresario.pdf',
                'url' => 'pensionado',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_11.pdf',
                'url' => 'pensionado',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
        ];
    }

    function __op_facultativo()
    {
        return [
            [
                'label' => 'Formulario',
                'file' => 'formulario',
                'url' => 'facultativo',
                'auto_generar' => true,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Oficio solicitud afiliación',
                'file' => 'carta_solicitud_afiliacion_empresario.pdf',
                'url' => 'facultativo',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
            [
                'label' => 'Tratamiento datos personales',
                'file' => 'formato_pm_psf_ft_11.pdf',
                'url' => 'facultativo',
                'auto_generar' => false,
                'firmas' => [
                    'T' => 'Trabajador',
                ],
            ],
        ];
    }
}

if (! function_exists('firmas_requeridas')) {

    function firmas_requeridas($tipafi)
    {
        switch ($tipafi) {
            case 'E':
            case 'U':
                return [
                    [
                        'code' => 'E',
                        'label' => 'Empleador',
                        'estado' => 'P',
                    ],
                ];
                break;
            case 'T':
            case 'C':
            case 'B':
                return [
                    [
                        'code' => 'E',
                        'label' => 'Empleador',
                        'estado' => 'P',
                    ],
                    [
                        'code' => 'T',
                        'label' => 'Trabajador',
                        'estado' => 'P',
                    ],
                ];
                break;
            case 'O':
            case 'I':
            case 'F':
                return [
                    [
                        'code' => 'T',
                        'label' => 'Trabajador',
                        'estado' => 'P',
                    ],
                ];
                break;
            default:
                return false;
                break;
        }
    }
}
