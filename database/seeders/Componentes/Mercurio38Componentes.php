<?php

return [
    // Campos del sistema (ocultos)
    [
        'formulario_id' => 6,
        'name' => 'calemp',
        'type' => 'hidden',
        'label' => 'Calificación Afiliado',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 0,
        'default_value' => 'P',  // Valor por defecto para pensionados
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Calificación del afiliado pensionado',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    [
        'formulario_id' => 6,
        'name' => 'coddocrepleg',
        'type' => 'hidden',
        'label' => 'Tipo Doc. Rep. Legal',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Tipo documento representante legal',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    [
        'formulario_id' => 6,
        'name' => 'fecsol',
        'type' => 'hidden',
        'label' => 'Fecha de Solicitud',
        'placeholder' => '',
        'form_type' => 'date',
        'group_id' => 0,
        'order' => 2,
        'default_value' => date('Y-m-d'),
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Fecha de solicitud',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],

    // Grupo 1: Datos de Identificación
    [
        'formulario_id' => 6,
        'name' => 'tipdoc',
        'type' => 'select',
        'label' => 'Tipo de Documento',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 1,
        'default_value' => 'CC',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'CC', 'label' => 'Cédula de Ciudadanía'],
            ['value' => 'CE', 'label' => 'Cédula de Extranjería'],
            ['value' => 'PA', 'label' => 'Pasaporte'],
            ['value' => 'TI', 'label' => 'Tarjeta de Identidad']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de documento de identidad',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de documento es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'cedtra',
        'type' => 'number',
        'label' => 'Número de Documento',
        'placeholder' => 'Ingrese el número de documento',
        'form_type' => 'input',
        'group_id' => 1,
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
            'error_messages' => [
                'required' => 'El número de documento es obligatorio',
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'prinom',
        'type' => 'text',
        'label' => 'Primer Nombre',
        'placeholder' => 'Ingrese el primer nombre',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer nombre del pensionado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El primer nombre es obligatorio',
                'min_length' => 'El nombre debe tener al menos 2 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'segnom',
        'type' => 'text',
        'label' => 'Segundo Nombre',
        'placeholder' => 'Ingrese el segundo nombre (opcional)',
        'form_type' => 'input',
        'group_id' => 1,
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
        'formulario_id' => 6,
        'name' => 'priape',
        'type' => 'text',
        'label' => 'Primer Apellido',
        'placeholder' => 'Ingrese el primer apellido',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer apellido del pensionado',
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
        'formulario_id' => 6,
        'name' => 'segape',
        'type' => 'text',
        'label' => 'Segundo Apellido',
        'placeholder' => 'Ingrese el segundo apellido (opcional)',
        'form_type' => 'input',
        'group_id' => 1,
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
        'formulario_id' => 6,
        'name' => 'fecnac',
        'type' => 'date',
        'label' => 'Fecha de Nacimiento',
        'placeholder' => 'Seleccione la fecha de nacimiento',
        'form_type' => 'date',
        'group_id' => 1,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha de nacimiento del pensionado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'sexo',
        'type' => 'select',
        'label' => 'Sexo',
        'placeholder' => 'Seleccione el sexo',
        'form_type' => 'select',
        'group_id' => 1,
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
        'help_text' => 'Seleccione el sexo del pensionado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El sexo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'orisex',
        'type' => 'select',
        'label' => 'Orientación Sexual',
        'placeholder' => 'Seleccione la orientación sexual',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 9,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'HETEROSEXUAL', 'label' => 'Heterosexual'],
            ['value' => 'HOMOSEXUAL', 'label' => 'Homosexual'],
            ['value' => 'BISEXUAL', 'label' => 'Bisexual'],
            ['value' => 'PANSEXUAL', 'label' => 'Pansexual'],
            ['value' => 'ASEXUAL', 'label' => 'Asexual'],
            ['value' => 'OTRO', 'label' => 'Otro'],
            ['value' => 'PREFIERO_NO_DECIR', 'label' => 'Prefiero no decirlo']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la orientación sexual',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'estciv',
        'type' => 'select',
        'label' => 'Estado Civil',
        'placeholder' => 'Seleccione el estado civil',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 10,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'SOLTERO', 'label' => 'Soltero(a)'],
            ['value' => 'CASADO', 'label' => 'Casado(a)'],
            ['value' => 'UNION_LIBRE', 'label' => 'Unión Libre'],
            ['value' => 'SEPARADO', 'label' => 'Separado(a)'],
            ['value' => 'DIVORCIADO', 'label' => 'Divorciado(a)'],
            ['value' => 'VIUDO', 'label' => 'Viudo(a)']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el estado civil',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El estado civil es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'cabhog',
        'type' => 'select',
        'label' => '¿Es cabeza de hogar?',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 11,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si es la persona que sostiene económicamente el hogar',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],

    // Grupo 2: Información de Ubicación
    [
        'formulario_id' => 6,
        'name' => 'codciu',
        'type' => 'select',
        'label' => 'Ciudad de Residencia',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 2,
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
        'formulario_id' => 6,
        'name' => 'direccion',
        'type' => 'textarea',
        'label' => 'Dirección de Residencia',
        'placeholder' => 'Ingrese la dirección completa',
        'form_type' => 'textarea',
        'group_id' => 2,
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
    [
        'formulario_id' => 6,
        'name' => 'dirlab',
        'type' => 'textarea',
        'label' => 'Dirección Laboral',
        'placeholder' => 'Ingrese la dirección laboral',
        'form_type' => 'textarea',
        'group_id' => 2,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la dirección del lugar donde trabajaba',
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
        'formulario_id' => 6,
        'name' => 'codzon',
        'type' => 'select',
        'label' => 'Zona de Trabajo',
        'placeholder' => 'Seleccione la zona',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 4,
        'default_value' => 'URBANA',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'URBANA', 'label' => 'Urbana'],
            ['value' => 'RURAL', 'label' => 'Rural']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la zona donde laboraba',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La zona es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'rural',
        'type' => 'select',
        'label' => 'Residencia Rural',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 5,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => '¿Vive en zona rural?',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'ruralt',
        'type' => 'select',
        'label' => 'Trabajo Rural',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 6,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => '¿Trabajaba en zona rural?',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],

    // Grupo 3: Información Laboral y de Pensión
    [
        'formulario_id' => 6,
        'name' => 'codact',
        'type' => 'select',
        'label' => 'Actividad Económica (CIUU)',
        'placeholder' => 'Seleccione la actividad económica',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la actividad económica según CIUU',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La actividad económica es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'cargo',
        'type' => 'select',
        'label' => 'Último Cargo',
        'placeholder' => 'Seleccione el cargo',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el último cargo que desempeñó',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El cargo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'fecini',
        'type' => 'date',
        'label' => 'Fecha de Inicio',
        'placeholder' => 'Seleccione la fecha',
        'form_type' => 'date',
        'group_id' => 3,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Fecha de inicio de labores',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de inicio es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'salario',
        'type' => 'number',
        'label' => 'Último Salario',
        'placeholder' => 'Ingrese el último salario',
        'form_type' => 'number',
        'group_id' => 3,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el último salario devengado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 0,
            'max_length' => 999999999,
            'field_size' => 12,
            'detail_info' => 'Último salario devengado por el pensionado',
            'numeric_range' => '0-999999999',
            'error_messages' => [
                'required' => 'El salario es obligatorio',
                'min_length' => 'El valor no puede ser negativo',
                'max_length' => 'El valor no puede exceder $999,999,999',
                'numeric' => 'Debe ser un valor numérico válido'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'tipsal',
        'type' => 'select',
        'label' => 'Tipo de Salario',
        'placeholder' => 'Seleccione el tipo',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 5,
        'default_value' => 'FIJO',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'FIJO', 'label' => 'Fijo'],
            ['value' => 'VARIABLE', 'label' => 'Variable'],
            ['value' => 'MIXTO', 'label' => 'Mixto']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de salario que recibía',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 4,
            'max_length' => 8,
            'field_size' => 8,
            'detail_info' => 'Tipo de salario que recibía el pensionado',
            'error_messages' => [
                'required' => 'El tipo de salario es obligatorio',
                'min_length' => 'Seleccione una opción válida',
                'max_length' => 'El valor seleccionado no es válido'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'codcaj',
        'type' => 'select',
        'label' => 'Caja de Compensación',
        'placeholder' => 'Seleccione la caja',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la caja de compensación a la que estuvo afiliado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La caja de compensación es obligatoria'
            ]
        ]
    ],

    // Grupo 4: Información de Salud y Educación
    [
        'formulario_id' => 6,
        'name' => 'captra',
        'type' => 'select',
        'label' => 'Capacidad de Trabajo',
        'placeholder' => 'Seleccione la capacidad',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 1,
        'default_value' => 'NO',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'SI', 'label' => 'Sí puede trabajar'],
            ['value' => 'NO', 'label' => 'No puede trabajar']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si la persona tiene capacidad para trabajar',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'tipdis',
        'type' => 'select',
        'label' => 'Tipo de Discapacidad',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 2,
        'default_value' => 'NINGUNA',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'NINGUNA', 'label' => 'Ninguna'],
            ['value' => 'FISICA', 'label' => 'Física'],
            ['value' => 'AUDITIVA', 'label' => 'Auditiva'],
            ['value' => 'VISUAL', 'label' => 'Visual'],
            ['value' => 'INTELECTUAL', 'label' => 'Intelectual'],
            ['value' => 'PSICOSOCIAL', 'label' => 'Psicosocial']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de discapacidad si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'nivedu',
        'type' => 'select',
        'label' => 'Nivel Educativo',
        'placeholder' => 'Seleccione el nivel',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'NINGUNO', 'label' => 'Ninguno'],
            ['value' => 'PRIMARIA', 'label' => 'Primaria'],
            ['value' => 'SECUNDARIA', 'label' => 'Secundaria'],
            ['value' => 'TECNICO', 'label' => 'Técnico'],
            ['value' => 'TECNOLOGO', 'label' => 'Tecnólogo'],
            ['value' => 'UNIVERSITARIO', 'label' => 'Universitario'],
            ['value' => 'POSGRADO', 'label' => 'Posgrado']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el máximo nivel educativo alcanzado',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El nivel educativo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'vivienda',
        'type' => 'select',
        'label' => 'Tipo de Vivienda',
        'placeholder' => 'Seleccione el tipo',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'PROPIA', 'label' => 'Propia'],
            ['value' => 'ARRIENDO', 'label' => 'Arrendada'],
            ['value' => 'FAMILIAR', 'label' => 'Familiar'],
            ['value' => 'OTRA', 'label' => 'Otra']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de vivienda',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de vivienda es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'facvul',
        'type' => 'select',
        'label' => 'Factor de Vulnerabilidad',
        'placeholder' => 'Seleccione el factor',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 5,
        'default_value' => 'NINGUNO',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'NINGUNO', 'label' => 'Ninguno'],
            ['value' => 'DESPLAZAMIENTO', 'label' => 'Desplazamiento forzado'],
            ['value' => 'VICTIMA', 'label' => 'Víctima del conflicto'],
            ['value' => 'MADRE_CABEZA', 'label' => 'Madre cabeza de hogar'],
            ['value' => 'ADULTO_MAYOR', 'label' => 'Adulto mayor'],
            ['value' => 'OTRO', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si aplica algún factor de vulnerabilidad',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],

    // Grupo 5: Información de Afiliación y Contacto
    [
        'formulario_id' => 6,
        'name' => 'tipafi',
        'type' => 'select',
        'label' => 'Tipo de Afiliado',
        'placeholder' => 'Seleccione el tipo',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 1,
        'default_value' => 'PENSIONADO',
        'is_disabled' => true,
        'is_readonly' => true,
        'data_source' => [
            ['value' => 'PENSIONADO', 'label' => 'Pensionado']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Tipo de afiliación',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de afiliado es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'peretn',
        'type' => 'select',
        'label' => 'Pertenencia Étnica',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 2,
        'default_value' => 'NINGUNO',
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
        'help_text' => 'Seleccione si pertenece a alguna etnia',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'resguardo_id',
        'type' => 'select',
        'label' => 'Resguardo Indígena',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 3,
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
        'formulario_id' => 6,
        'name' => 'pub_indigena_id',
        'type' => 'select',
        'label' => 'Pueblo Indígena',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 4,
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
        'formulario_id' => 6,
        'name' => 'email',
        'type' => 'email',
        'label' => 'Correo Electrónico',
        'placeholder' => 'correo@ejemplo.com',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese un correo electrónico válido para notificaciones',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El correo electrónico es obligatorio',
                'email' => 'Ingrese un correo electrónico válido'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'telefono',
        'type' => 'tel',
        'label' => 'Teléfono Fijo',
        'placeholder' => 'Ingrese el teléfono fijo',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de teléfono fijo con indicativo',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '^[0-9]{7,15}$',
            'error_messages' => [
                'pattern' => 'Ingrese un número de teléfono válido (solo números, 7-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'celular',
        'type' => 'tel',
        'label' => 'Celular',
        'placeholder' => 'Ingrese el número de celular',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de celular con indicativo',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '^[0-9]{10,15}$',
            'error_messages' => [
                'required' => 'El celular es obligatorio',
                'pattern' => 'Ingrese un número de celular válido (solo números, 10-15 dígitos)'
            ]
        ]
    ],

    // Grupo 6: Información Bancaria
    [
        'formulario_id' => 6,
        'name' => 'tippag',
        'type' => 'select',
        'label' => 'Tipo de Pago de Pensión',
        'placeholder' => 'Seleccione el tipo de pago',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 1,
        'default_value' => 'CUENTA_AHORROS',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'CUENTA_AHORROS', 'label' => 'Cuenta de Ahorros'],
            ['value' => 'CUENTA_CORRIENTE', 'label' => 'Cuenta Corriente'],
            ['value' => 'DAVIPLATA', 'label' => 'DaviPlata'],
            ['value' => 'EFECTIVO', 'label' => 'Efectivo']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione cómo desea recibir los pagos de pensión',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de pago es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'numcue',
        'type' => 'text',
        'label' => 'Número de Cuenta',
        'placeholder' => 'Ingrese el número de cuenta',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de cuenta bancaria o celular DaviPlata',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 5,
            'max_length' => 30,
            'error_messages' => [
                'min_length' => 'El número de cuenta debe tener al menos 5 caracteres',
                'max_length' => 'El número de cuenta no puede exceder 30 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 6,
        'name' => 'tipcue',
        'type' => 'select',
        'label' => 'Tipo de Cuenta',
        'placeholder' => 'Seleccione el tipo de cuenta',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 3,
        'default_value' => 'AHORROS',
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
        'formulario_id' => 6,
        'name' => 'codban',
        'type' => 'select',
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
    [
        'formulario_id' => 6,
        'name' => 'autoriza',
        'type' => 'select',
        'label' => 'Autoriza Tratamiento de Datos',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 5,
        'default_value' => 'S',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí, autorizo el tratamiento de mis datos personales'],
            ['value' => 'N', 'label' => 'No autorizo el tratamiento de mis datos personales']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Autorización para el tratamiento de datos personales',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La autorización es obligatoria'
            ]
        ]
    ]
];
