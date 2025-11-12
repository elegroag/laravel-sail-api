<?php

return [
    // Campo oculto del sistema
    [
        'formulario_id' => 2,
        'name' => 'profesion',
        'type' => 'hidden',
        'label' => 'Profesión',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0, // Grupo 0 para campos del sistema
        'order' => 0,
        'default_value' => 'Ninguna',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Profesión del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => false
        ]
    ],

    // Grupo 1: Datos Básicos del Trabajador
    [
        'formulario_id' => 2,
        'name' => 'tipdoc',
        'type' => 'text',
        'label' => 'Tipo de Documento',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 1,
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
        'help_text' => 'Seleccione el tipo de documento de identidad',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 1,
            'max_length' => 2,
            'field_size' => 2,
            'detail_info' => 'Tipo de documento de identidad del trabajador',
            'error_messages' => [
                'required' => 'El tipo de documento es obligatorio',
                'min_length' => 'Seleccione un tipo de documento válido',
                'max_length' => 'El tipo de documento no puede tener más de 2 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
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
            'field_size' => 20,
            'detail_info' => 'Número de documento de identidad del trabajador',
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
        'formulario_id' => 2,
        'name' => 'priape',
        'type' => 'text',
        'label' => 'Primer Apellido',
        'placeholder' => 'Ingrese el primer apellido',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer apellido',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'field_size' => 100,
            'detail_info' => 'Primer apellido del trabajador',
            'pattern' => '^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'-]+$',
            'error_messages' => [
                'required' => 'El primer apellido es obligatorio',
                'min_length' => 'El apellido debe tener al menos 2 caracteres',
                'max_length' => 'El apellido no puede exceder 100 caracteres',
                'pattern' => 'Solo se permiten letras, espacios y guiones'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'segape',
        'type' => 'text',
        'label' => 'Segundo Apellido',
        'placeholder' => 'Ingrese el segundo apellido (opcional)',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo apellido si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 2,
            'max_length' => 100,
            'field_size' => 100,
            'detail_info' => 'Segundo apellido del trabajador (opcional)',
            'pattern' => '^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'-]*$',
            'error_messages' => [
                'min_length' => 'El apellido debe tener al menos 2 caracteres',
                'max_length' => 'El apellido no puede exceder 100 caracteres',
                'pattern' => 'Solo se permiten letras, espacios y guiones'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'prinom',
        'type' => 'text',
        'label' => 'Primer Nombre',
        'placeholder' => 'Ingrese el primer nombre',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer nombre',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'field_size' => 100,
            'detail_info' => 'Primer nombre del trabajador',
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
        'formulario_id' => 2,
        'name' => 'segnom',
        'type' => 'text',
        'label' => 'Segundo Nombre',
        'placeholder' => 'Ingrese el segundo nombre (opcional)',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo nombre si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 2,
            'max_length' => 100,
            'field_size' => 100,
            'detail_info' => 'Segundo nombre del trabajador (opcional)',
            'pattern' => '^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'-]*$',
            'error_messages' => [
                'min_length' => 'El nombre debe tener al menos 2 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres',
                'pattern' => 'Solo se permiten letras, espacios y guiones'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'sexo',
        'type' => 'text',
        'label' => 'Sexo',
        'placeholder' => 'Seleccione el sexo',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'M', 'label' => 'Masculino'],
            ['value' => 'F', 'label' => 'Femenino'],
            ['value' => 'O', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el sexo del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El sexo es un campo obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'fecnac',
        'type' => 'date',
        'label' => 'Fecha de Nacimiento',
        'placeholder' => 'Seleccione la fecha de nacimiento',
        'form_type' => 'date',
        'group_id' => 1,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha de nacimiento del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'ciunac',
        'type' => 'text',
        'label' => 'Ciudad de Nacimiento',
        'placeholder' => 'Seleccione la ciudad de nacimiento',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 9,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la ciudad de nacimiento',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La ciudad de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'estciv',
        'type' => 'text',
        'label' => 'Estado Civil',
        'placeholder' => 'Seleccione el estado civil',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 10,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Soltero/a'],
            ['value' => 'C', 'label' => 'Casado/a'],
            ['value' => 'U', 'label' => 'Unión Libre'],
            ['value' => 'D', 'label' => 'Divorciado/a'],
            ['value' => 'V', 'label' => 'Viudo/a'],
            ['value' => 'SE', 'label' => 'Separado/a']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el estado civil del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El estado civil es obligatorio'
            ]
        ]
    ],

    // Grupo 2: Datos Laborales
    [
        'formulario_id' => 2,
        'name' => 'fecing',
        'type' => 'date',
        'label' => 'Fecha de Ingreso',
        'placeholder' => 'Seleccione la fecha de ingreso',
        'form_type' => 'date',
        'group_id' => 2,
        'order' => 1,
        'default_value' => date('Y-m-d'),
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha en que el trabajador ingresó a la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de ingreso es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'cargo',
        'type' => 'text',
        'label' => 'Cargo',
        'placeholder' => 'Seleccione el cargo',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el cargo del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El cargo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'salario',
        'type' => 'number',
        'label' => 'Salario',
        'placeholder' => 'Ingrese el salario',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el salario del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 0,
            'max_length' => 999999999,
            'field_size' => 12,
            'detail_info' => 'Salario mensual del trabajador',
            'numeric_range' => '0-999999999',
            'error_messages' => [
                'required' => 'El salario es obligatorio',
                'min_length' => 'El salario no puede ser negativo',
                'max_length' => 'El valor no puede exceder $999,999,999',
                'pattern' => 'Debe ser un valor numérico válido'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'tipsal',
        'type' => 'text',
        'label' => 'Tipo de Salario',
        'placeholder' => 'Seleccione el tipo de salario',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'FIJO', 'label' => 'Fijo'],
            ['value' => 'VARIABLE', 'label' => 'Variable'],
            ['value' => 'MIXTO', 'label' => 'Mixto']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de salario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de salario es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'horas',
        'type' => 'number',
        'label' => 'Horas Mensuales',
        'placeholder' => 'Ingrese las horas mensuales',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de horas mensuales laboradas',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 1,
            'max_length' => 3,
            'field_size' => 3,
            'detail_info' => 'Número de horas mensuales laboradas (1-240)',
            'pattern' => '^[0-9]+$',
            'numeric_range' => '1-240',
            'error_messages' => [
                'required' => 'Las horas mensuales son obligatorias',
                'min_length' => 'Las horas no pueden ser menores a 1',
                'max_length' => 'Máximo 3 dígitos permitidos',
                'pattern' => 'Solo se permiten números enteros',
                'numeric_range' => 'El valor debe estar entre 1 y 240 horas'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'tipcon',
        'type' => 'text',
        'label' => 'Tipo de Contrato',
        'placeholder' => 'Seleccione el tipo de contrato',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'FIJO', 'label' => 'Término Fijo'],
            ['value' => 'INDEFINIDO', 'label' => 'Término Indefinido'],
            ['value' => 'OBRA', 'label' => 'Por Obra o Labor'],
            ['value' => 'APRENDIZAJE', 'label' => 'Contrato de Aprendizaje'],
            ['value' => 'OCASIONAL', 'label' => 'Ocasional, Temporal o Accidental']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de contrato',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de contrato es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'tipjor',
        'type' => 'text',
        'label' => 'Tipo de Jornada',
        'placeholder' => 'Seleccione el tipo de jornada',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'COMPLETA', 'label' => 'Jornada Completa'],
            ['value' => 'MEDIA', 'label' => 'Media Jornada'],
            ['value' => 'TURNOS', 'label' => 'Por Turnos'],
            ['value' => 'NOCTURNA', 'label' => 'Nocturna'],
            ['value' => 'FLEXIBLE', 'label' => 'Horario Flexible']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de jornada laboral',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de jornada es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'comision',
        'type' => 'text',
        'label' => 'Recibe Comisión',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 8,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si el trabajador recibe comisiones',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'labora_otra_empresa',
        'type' => 'text',
        'label' => 'Labora en Otra Empresa',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 9,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si el trabajador labora en otra empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'otra_empresa',
        'type' => 'text',
        'label' => 'Empresa donde también labora',
        'placeholder' => 'Ingrese el nombre de la otra empresa (si aplica)',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 10,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el nombre de la otra empresa donde labora (opcional)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 200,
            'error_messages' => [
                'max_length' => 'El nombre de la empresa no puede exceder 200 caracteres'
            ]
        ]
    ],

    // Grupo 3: Información de Contacto
    [
        'formulario_id' => 2,
        'name' => 'telefono',
        'type' => 'tel',
        'label' => 'Teléfono',
        'placeholder' => 'Ingrese el número de teléfono',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de teléfono con código de área',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '^[0-9]{7,15}$',
            'error_messages' => [
                'required' => 'El teléfono es obligatorio',
                'pattern' => 'Ingrese un número de teléfono válido (solo números, 7-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'celular',
        'type' => 'tel',
        'label' => 'Celular',
        'placeholder' => 'Ingrese el número de celular',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 2,
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
    [
        'formulario_id' => 2,
        'name' => 'email',
        'type' => 'email',
        'label' => 'Correo Electrónico',
        'placeholder' => 'correo@ejemplo.com',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese un correo electrónico válido',
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
        'formulario_id' => 2,
        'name' => 'direccion',
        'type' => 'text',
        'label' => 'Dirección de Residencia',
        'placeholder' => 'Ingrese la dirección completa',
        'form_type' => 'textarea',
        'group_id' => 3,
        'order' => 4,
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
        'formulario_id' => 2,
        'name' => 'codciu',
        'type' => 'text',
        'label' => 'Ciudad de Residencia',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 5,
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
        'formulario_id' => 2,
        'name' => 'barrio',
        'type' => 'text',
        'label' => 'Barrio',
        'placeholder' => 'Ingrese el barrio',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el nombre del barrio',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 3,
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El barrio es obligatorio',
                'min_length' => 'El barrio debe tener al menos 3 caracteres',
                'max_length' => 'El barrio no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'rural',
        'type' => 'text',
        'label' => 'Residencia Rural',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 7,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si la residencia es en zona rural',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'vivienda',
        'type' => 'text',
        'label' => 'Tipo de Vivienda',
        'placeholder' => 'Seleccione el tipo de vivienda',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'PROPIA', 'label' => 'Propia'],
            ['value' => 'ARRENDADA', 'label' => 'Arrendada'],
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

    // Grupo 4: Información Adicional
    [
        'formulario_id' => 2,
        'name' => 'orisex',
        'type' => 'text',
        'label' => 'Orientación Sexual',
        'placeholder' => 'Seleccione la orientación sexual',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'HETERO', 'label' => 'Heterosexual'],
            ['value' => 'HOMO', 'label' => 'Homosexual'],
            ['value' => 'BI', 'label' => 'Bisexual'],
            ['value' => 'OTRO', 'label' => 'Otro'],
            ['value' => 'PREFIERO_NO_DECIR', 'label' => 'Prefiero no decirlo']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la orientación sexual (opcional)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'facvul',
        'type' => 'text',
        'label' => 'Factor de Vulnerabilidad',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 2,
        'default_value' => 'NINGUNO',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'NINGUNO', 'label' => 'Ninguno'],
            ['value' => 'DISCAPACIDAD', 'label' => 'Persona con discapacidad'],
            ['value' => 'VICTIMA', 'label' => 'Víctima del conflicto'],
            ['value' => 'DESPLAZADO', 'label' => 'Desplazado'],
            ['value' => 'MADRE_CABEZA', 'label' => 'Madre cabeza de familia'],
            ['value' => 'ADULTO_MAYOR', 'label' => 'Adulto mayor'],
            ['value' => 'OTRO', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si aplica algún factor de vulnerabilidad',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'tipafi',
        'type' => 'text',
        'label' => 'Tipo de Afiliado',
        'placeholder' => 'Seleccione el tipo de afiliación',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 3,
        'default_value' => 'COTIZANTE',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'COTIZANTE', 'label' => 'Cotizante'],
            ['value' => 'BENEFICIARIO', 'label' => 'Beneficiario']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de afiliación',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de afiliación es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 2,
        'name' => 'autoriza',
        'type' => 'text',
        'label' => 'Autorización de Tratamiento de Datos',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 4,
        'default_value' => 'S',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí, autorizo el tratamiento de mis datos personales'],
            ['value' => 'N', 'label' => 'No autorizo el tratamiento de mis datos personales']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si autoriza el tratamiento de sus datos personales',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Debe indicar si autoriza el tratamiento de datos personales'
            ]
        ]
    ]
];
