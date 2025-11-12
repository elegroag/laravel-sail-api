<?php

return [
    // Campos del sistema (ocultos)
    [
        'formulario_id' => 4,
        'name' => 'profesion',
        'type' => 'hidden',
        'label' => 'Profesión',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 0,
        'default_value' => 'Ninguna',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Profesión del beneficiario',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    [
        'formulario_id' => 4,
        'name' => 'fax',
        'type' => 'hidden',
        'label' => 'Fax',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Fax',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],

    // Grupo 1: Datos de la Empresa
    [
        'formulario_id' => 4,
        'name' => 'nit',
        'type' => 'number',
        'label' => 'NIT Empresa/Empleador',
        'placeholder' => 'Ingrese el NIT de la empresa',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'NIT de la empresa o empleador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Número de Identificación Tributaria de la empresa o empleador',
            'pattern' => '^[0-9-]+$',
            'error_messages' => [
                'required' => 'El NIT es obligatorio',
                'min_length' => 'El NIT debe tener al menos 5 dígitos',
                'max_length' => 'El NIT no puede exceder 20 caracteres',
                'pattern' => 'Solo se permiten números y guiones'
            ]
        ]
    ],

    // Grupo 2: Relación con el Trabajador
    [
        'formulario_id' => 4,
        'name' => 'cedtra',
        'type' => 'number',
        'label' => 'Identificación del Trabajador',
        'placeholder' => 'Ingrese la identificación del trabajador',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Documento de identidad del trabajador titular',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Documento de identidad del trabajador titular',
            'pattern' => '^[0-9]+$',
            'error_messages' => [
                'required' => 'La identificación del trabajador es obligatoria',
                'min_length' => 'La identificación debe tener al menos 5 dígitos',
                'max_length' => 'La identificación no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números sin puntos ni comas'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'parent',
        'type' => 'text',
        'label' => 'Parentesco con el Trabajador',
        'placeholder' => 'Seleccione el parentesco',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'HIJO', 'label' => 'Hijo(a)'],
            ['value' => 'HIJASTRO', 'label' => 'Hijastro(a)'],
            ['value' => 'PADRE', 'label' => 'Padre'],
            ['value' => 'MADRE', 'label' => 'Madre'],
            ['value' => 'CONYUGE', 'label' => 'Cónyuge'],
            ['value' => 'HERMANO', 'label' => 'Hermano(a)'],
            ['value' => 'NIETO', 'label' => 'Nieto(a)'],
            ['value' => 'OTRO', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el parentesco con el trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Relación de parentesco con el trabajador titular',
            'error_messages' => [
                'required' => 'El parentesco es obligatorio',
                'min_length' => 'Seleccione una opción válida',
                'max_length' => 'El valor seleccionado no es válido'
            ]
        ]
    ],

    // Grupo 3: Datos del Beneficiario
    [
        'formulario_id' => 4,
        'name' => 'tipdoc',
        'type' => 'text',
        'label' => 'Tipo de Documento',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'RC', 'label' => 'Registro Civil'],
            ['value' => 'TI', 'label' => 'Tarjeta de Identidad'],
            ['value' => 'CC', 'label' => 'Cédula de Ciudadanía'],
            ['value' => 'CE', 'label' => 'Cédula de Extranjería'],
            ['value' => 'PA', 'label' => 'Pasaporte']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de documento de identidad',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 2,
            'field_size' => 2,
            'detail_info' => 'Tipo de documento de identificación del beneficiario',
            'error_messages' => [
                'required' => 'El tipo de documento es obligatorio',
                'min_length' => 'Seleccione una opción válida',
                'max_length' => 'El código del documento debe tener 2 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'numdoc',
        'type' => 'number',
        'label' => 'Número de Documento',
        'placeholder' => 'Ingrese el número de documento',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de documento sin puntos ni comas',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Número de documento de identificación del beneficiario',
            'pattern' => '^[0-9]+$',
            'error_messages' => [
                'required' => 'El número de documento es obligatorio',
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números sin puntos ni comas'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'prinom',
        'type' => 'text',
        'label' => 'Primer Nombre',
        'placeholder' => 'Ingrese el primer nombre',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer nombre del beneficiario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'field_size' => 100,
            'detail_info' => 'Primer nombre del beneficiario',
            'pattern' => '^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'-]+$',
            'error_messages' => [
                'required' => 'El primer nombre es obligatorio',
                'min_length' => 'El nombre debe tener al menos 2 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres',
                'pattern' => 'Solo se permiten letras, espacios y guiones'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'segnom',
        'type' => 'text',
        'label' => 'Segundo Nombre',
        'placeholder' => 'Ingrese el segundo nombre (opcional)',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo nombre si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'priape',
        'type' => 'text',
        'label' => 'Primer Apellido',
        'placeholder' => 'Ingrese el primer apellido',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer apellido del beneficiario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El primer apellido es obligatorio',
                'min_length' => 'El apellido debe tener al menos 2 caracteres',
                'max_length' => 'El apellido no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'segape',
        'type' => 'text',
        'label' => 'Segundo Apellido',
        'placeholder' => 'Ingrese el segundo apellido (opcional)',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo apellido si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'fecnac',
        'type' => 'date',
        'label' => 'Fecha de Nacimiento',
        'placeholder' => 'Seleccione la fecha de nacimiento',
        'form_type' => 'date',
        'group_id' => 3,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha de nacimiento del beneficiario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'sexo',
        'type' => 'text',
        'label' => 'Sexo',
        'placeholder' => 'Seleccione el sexo',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'M', 'label' => 'Masculino'],
            ['value' => 'F', 'label' => 'Femenino'],
            ['value' => 'O', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el sexo del beneficiario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El sexo es obligatorio'
            ]
        ]
    ],

    // Grupo 4: Información de Contacto
    [
        'formulario_id' => 4,
        'name' => 'telefono',
        'type' => 'tel',
        'label' => 'Teléfono Fijo',
        'placeholder' => 'Ingrese el teléfono fijo',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de teléfono fijo con indicativo',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '^[0-9]{7,15]$',
            'error_messages' => [
                'pattern' => 'Ingrese un número de teléfono válido (solo números, 7-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'celular',
        'type' => 'tel',
        'label' => 'Celular',
        'placeholder' => 'Ingrese el número de celular',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de celular con indicativo',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '^[0-9]{10,15]$',
            'error_messages' => [
                'required' => 'El celular es obligatorio',
                'pattern' => 'Ingrese un número de celular válido (solo números, 10-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'email',
        'type' => 'email',
        'label' => 'Correo Electrónico',
        'placeholder' => 'correo@ejemplo.com',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese un correo electrónico válido',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => [
                'email' => 'Ingrese un correo electrónico válido'
            ]
        ]
    ],

    // Grupo 5: Información de Residencia
    [
        'formulario_id' => 4,
        'name' => 'codciu',
        'type' => 'text',
        'label' => 'Ciudad de Residencia',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la ciudad de residencia',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La ciudad de residencia es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'direccion',
        'type' => 'text',
        'label' => 'Dirección de Residencia',
        'placeholder' => 'Ingrese la dirección completa',
        'form_type' => 'textarea',
        'group_id' => 5,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la dirección completa de residencia',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 10,
            'max_length' => 255,
            'error_messages' => [
                'required' => 'La dirección es obligatoria',
                'min_length' => 'La dirección debe tener al menos 10 caracteres',
                'max_length' => 'La dirección no puede exceder 255 caracteres'
            ]
        ]
    ],

    // Grupo 6: Información Bancaria
    [
        'formulario_id' => 4,
        'name' => 'tippag',
        'type' => 'text',
        'label' => 'Tipo de Medio de Pago',
        'placeholder' => 'Seleccione el medio de pago',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'CUENTA_AHORROS', 'label' => 'Cuenta de Ahorros'],
            ['value' => 'CUENTA_CORRIENTE', 'label' => 'Cuenta Corriente'],
            ['value' => 'DAVIPLATA', 'label' => 'DaviPlata'],
            ['value' => 'EFECTIVO', 'label' => 'Efectivo']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el medio de pago para el subsidio',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El medio de pago es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'numcue',
        'type' => 'text',
        'label' => 'Número de Cuenta/Daviplata',
        'placeholder' => 'Ingrese el número de cuenta o celular DaviPlata',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de cuenta o celular según el medio de pago seleccionado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 30,
            'error_messages' => [
                'required' => 'El número de cuenta es obligatorio',
                'min_length' => 'El número de cuenta debe tener al menos 5 caracteres',
                'max_length' => 'El número de cuenta no puede exceder 30 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'tipcue',
        'type' => 'text',
        'label' => 'Tipo de Cuenta',
        'placeholder' => 'Seleccione el tipo de cuenta',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'AHORROS', 'label' => 'Ahorros'],
            ['value' => 'CORRIENTE', 'label' => 'Corriente']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de cuenta bancaria',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'codban',
        'type' => 'text',
        'label' => 'Banco',
        'placeholder' => 'Seleccione el banco',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el banco de la cuenta',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],

    // Grupo 7: Información de los Padres Biológicos
    [
        'formulario_id' => 4,
        'name' => 'biodesco',
        'type' => 'text',
        'label' => '¿Desconoce ubicación del padre/madre biológico?',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 1,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si desconoce la ubicación del padre/madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biocedu',
        'type' => 'number',
        'label' => 'Cédula Padre/Madre Biológico',
        'placeholder' => 'Ingrese el número de documento',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el documento de identidad del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 5,
            'max_length' => 20,
            'error_messages' => [
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biotipdoc',
        'type' => 'text',
        'label' => 'Tipo Documento Padre/Madre',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'CC', 'label' => 'Cédula de Ciudadanía'],
            ['value' => 'CE', 'label' => 'Cédula de Extranjería'],
            ['value' => 'PA', 'label' => 'Pasaporte'],
            ['value' => 'TI', 'label' => 'Tarjeta de Identidad'],
            ['value' => 'RC', 'label' => 'Registro Civil']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de documento del padre/madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'bioprinom',
        'type' => 'text',
        'label' => 'Primer Nombre',
        'placeholder' => 'Primer nombre del padre/madre',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Primer nombre del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biosegnom',
        'type' => 'text',
        'label' => 'Segundo Nombre',
        'placeholder' => 'Segundo nombre (opcional)',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Segundo nombre del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biopriape',
        'type' => 'text',
        'label' => 'Primer Apellido',
        'placeholder' => 'Primer apellido del padre/madre',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Primer apellido del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biosegape',
        'type' => 'text',
        'label' => 'Segundo Apellido',
        'placeholder' => 'Segundo apellido (opcional)',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Segundo apellido del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El apellido no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'bioemail',
        'type' => 'email',
        'label' => 'Correo Electrónico',
        'placeholder' => 'correo@ejemplo.com',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Correo electrónico del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => [
                'email' => 'Ingrese un correo electrónico válido'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biophone',
        'type' => 'tel',
        'label' => 'Teléfono',
        'placeholder' => 'Ingrese el teléfono',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 9,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Número de teléfono del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '^[0-9]{7,15]$',
            'error_messages' => [
                'pattern' => 'Ingrese un número de teléfono válido (solo números, 7-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biocodciu',
        'type' => 'text',
        'label' => 'Ciudad de Residencia',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 10,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Ciudad de residencia del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biodire',
        'type' => 'text',
        'label' => 'Dirección de Residencia',
        'placeholder' => 'Ingrese la dirección',
        'form_type' => 'input',
        'group_id' => 7,
        'order' => 11,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Dirección de residencia del padre o madre biológico',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 255,
            'error_messages' => [
                'max_length' => 'La dirección no puede exceder 255 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'biourbana',
        'type' => 'text',
        'label' => 'Zona de Residencia',
        'placeholder' => 'Seleccione la zona',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 12,
        'default_value' => 'S',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Urbana'],
            ['value' => 'N', 'label' => 'Rural']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si la residencia es urbana o rural',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],

    // Grupo 8: Información Adicional
    [
        'formulario_id' => 4,
        'name' => 'huerfano',
        'type' => 'text',
        'label' => '¿Es huérfano?',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 1,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si el beneficiario es huérfano',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'tiphij',
        'type' => 'text',
        'label' => 'Tipo de Hijo',
        'placeholder' => 'Seleccione el tipo de hijo',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'BIOLOGICO', 'label' => 'Biológico'],
            ['value' => 'ADOPTIVO', 'label' => 'Adoptivo'],
            ['value' => 'ACOGIDA', 'label' => 'En acogida'],
            ['value' => 'PADRASTRO', 'label' => 'Hijastro(a)']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de filiación con el trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'peretn',
        'type' => 'text',
        'label' => 'Pertenencia Étnica',
        'placeholder' => 'Seleccione la etnia',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'NINGUNO', 'label' => 'Ninguno'],
            ['value' => 'INDIGENA', 'label' => 'Indígena'],
            ['value' => 'AFRO', 'label' => 'Afrodescendiente'],
            ['value' => 'RAIZAL', 'label' => 'Raizal'],
            ['value' => 'PALENQUERO', 'label' => 'Palenquero'],
            ['value' => 'ROM', 'label' => 'Gitano/Rom']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la pertenencia étnica si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'resguardo_id',
        'type' => 'text',
        'label' => 'Resguardo Indígena',
        'placeholder' => 'Seleccione el resguardo',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el resguardo indígena si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'pub_indigena_id',
        'type' => 'text',
        'label' => 'Pueblo Indígena',
        'placeholder' => 'Seleccione el pueblo indígena',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el pueblo indígena si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'convive',
        'type' => 'text',
        'label' => '¿Con quién convive?',
        'placeholder' => 'Seleccione con quién convive',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'PADRES', 'label' => 'Con ambos padres'],
            ['value' => 'PADRE', 'label' => 'Solo con el padre'],
            ['value' => 'MADRE', 'label' => 'Solo con la madre'],
            ['value' => 'TUTOR', 'label' => 'Con tutor legal'],
            ['value' => 'OTRO', 'label' => 'Con otro familiar']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione con quién convive el beneficiario',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'cedacu',
        'type' => 'number',
        'label' => 'Identificación del Acudiente',
        'placeholder' => 'Ingrese la identificación del acudiente',
        'form_type' => 'input',
        'group_id' => 8,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => true, // Solo lectura, se llenará automáticamente
        'css_classes' => 'form-control bg-light',
        'help_text' => 'Documento de identidad del acudiente (se llena automáticamente)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 5,
            'max_length' => 20,
            'error_messages' => [
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números'
            ]
        ]
    ],
    [
        'formulario_id' => 4,
        'name' => 'cedcon',
        'type' => 'number',
        'label' => 'Identificación del Padre/Madre Acudiente',
        'placeholder' => 'Ingrese la identificación del padre/madre',
        'form_type' => 'input',
        'group_id' => 8,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Documento de identidad del padre o madre acudiente',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 5,
            'max_length' => 20,
            'error_messages' => [
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números'
            ]
        ]
    ]
];
