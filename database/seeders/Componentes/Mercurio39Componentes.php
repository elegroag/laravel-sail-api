<?php

return [
    // Campos del sistema (ocultos)
    [
        'name' => 'calemp',
        'type' => 'hidden',
        'label' => 'Calificación',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 0,
        'default_value' => 'C',  // Valor por defecto para comunitarios
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Calificación del afiliado comunitario',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    [
        'name' => 'codact',
        'type' => 'hidden',
        'label' => 'Actividad Económica',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Actividad económica',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    
    // Grupo 1: Datos de Identificación
    [
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
        'help_text' => 'Ingrese el primer nombre del afiliado comunitario',
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
        'help_text' => 'Ingrese el primer apellido del afiliado comunitario',
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
        'help_text' => 'Seleccione la fecha de nacimiento del afiliado comunitario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'name' => 'ciunac',
        'type' => 'select',
        'label' => 'Ciudad de Nacimiento',
        'placeholder' => 'Seleccione la ciudad de nacimiento',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 8,
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
        'name' => 'sexo',
        'type' => 'select',
        'label' => 'Sexo',
        'placeholder' => 'Seleccione el sexo',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 9,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'M', 'label' => 'Masculino'],
            ['value' => 'F', 'label' => 'Femenino'],
            ['value' => 'O', 'label' => 'Otro']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el sexo del afiliado comunitario',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El sexo es obligatorio'
            ]
        ]
    ],
    [
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
        'name' => 'codzon',
        'type' => 'select',
        'label' => 'Zona',
        'placeholder' => 'Seleccione la zona',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 2,
        'default_value' => 'URBANA',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'URBANA', 'label' => 'Urbana'],
            ['value' => 'RURAL', 'label' => 'Rural']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la zona donde reside',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La zona es obligatoria'
            ]
        ]
    ],
    [
        'name' => 'direccion',
        'type' => 'textarea',
        'label' => 'Dirección',
        'placeholder' => 'Ingrese la dirección completa',
        'form_type' => 'textarea',
        'group_id' => 2,
        'order' => 3,
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
        'name' => 'barrio',
        'type' => 'text',
        'label' => 'Barrio',
        'placeholder' => 'Ingrese el nombre del barrio',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el nombre del barrio donde reside',
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
    
    // Grupo 3: Información de Contacto
    [
        'name' => 'telefono',
        'type' => 'tel',
        'label' => 'Teléfono Fijo',
        'placeholder' => 'Ingrese el teléfono fijo',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 1,
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
        'name' => 'fax',
        'type' => 'text',
        'label' => 'Fax',
        'placeholder' => 'Ingrese el número de fax',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de fax si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 20,
            'error_messages' => [
                'max_length' => 'El fax no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'name' => 'email',
        'type' => 'email',
        'label' => 'Correo Electrónico',
        'placeholder' => 'correo@ejemplo.com',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 4,
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
    
    // Grupo 4: Información Laboral y de Salud
    [
        'name' => 'fecing',
        'type' => 'date',
        'label' => 'Fecha de Ingreso',
        'placeholder' => 'Seleccione la fecha de ingreso',
        'form_type' => 'date',
        'group_id' => 4,
        'order' => 1,
        'default_value' => date('Y-m-d'),
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha de ingreso a la comunidad',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de ingreso es obligatoria'
            ]
        ]
    ],
    [
        'name' => 'salario',
        'type' => 'number',
        'label' => 'Ingresos Mensuales',
        'placeholder' => 'Ingrese los ingresos mensuales',
        'form_type' => 'number',
        'group_id' => 4,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el promedio de ingresos mensuales',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min' => 0,
            'error_messages' => [
                'required' => 'Los ingresos mensuales son obligatorios',
                'min' => 'El valor no puede ser negativo'
            ]
        ]
    ],
    [
        'name' => 'captra',
        'type' => 'select',
        'label' => 'Capacidad de Trabajo',
        'placeholder' => 'Seleccione la capacidad',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 3,
        'default_value' => 'SI',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'SI', 'label' => 'Sí puede trabajar'],
            ['value' => 'NO', 'label' => 'No puede trabajar']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si tiene capacidad para trabajar',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'Este campo es obligatorio'
            ]
        ]
    ],
    [
        'name' => 'tipdis',
        'type' => 'select',
        'label' => 'Tipo de Discapacidad',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 4,
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
        'name' => 'nivedu',
        'type' => 'select',
        'label' => 'Nivel Educativo',
        'placeholder' => 'Seleccione el nivel',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 5,
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
        'name' => 'vivienda',
        'type' => 'select',
        'label' => 'Tipo de Vivienda',
        'placeholder' => 'Seleccione el tipo',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 6,
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
        'name' => 'tipafi',
        'type' => 'select',
        'label' => 'Tipo de Afiliado',
        'placeholder' => 'Seleccione el tipo',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 7,
        'default_value' => 'COMUNITARIO',
        'is_disabled' => true,
        'is_readonly' => true,
        'data_source' => [
            ['value' => 'COMUNITARIO', 'label' => 'Comunitario']
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
        'name' => 'autoriza',
        'type' => 'select',
        'label' => 'Autoriza Tratamiento de Datos',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 8,
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
