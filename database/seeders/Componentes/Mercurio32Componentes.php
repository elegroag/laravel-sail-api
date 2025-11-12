<?php

return [
    // Campos del sistema (ocultos)
    [
        'formulario_id' => 3,
        'name' => 'profesion',
        'type' => 'hidden',
        'label' => 'Profesión',
        'placeholder' => '',
        'form_type' => 'hidden',
        'group_id' => 0,
        'order' => 0,
        'default_value' => '',
        'is_disabled' => true,
        'is_readonly' => true,
        'css_classes' => 'd-none',
        'help_text' => 'Campo de sistema - Profesión del cónyuge',
        'target' => -1,
        'validacion' => ['is_required' => false]
    ],
    [
        'formulario_id' => 3,
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

    // Grupo 1: Datos del Trabajador
    [
        'formulario_id' => 3,
        'name' => 'cedtra',
        'type' => 'number',
        'label' => 'Identificación del Trabajador',
        'placeholder' => 'Ingrese la identificación del trabajador',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Documento de identidad del trabajador',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Número de identificación del trabajador',
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
        'formulario_id' => 3,
        'name' => 'nit',
        'type' => 'number',
        'label' => 'NIT (cuando aplica)',
        'placeholder' => 'Ingrese el NIT si aplica',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Número de identificación tributaria si aplica',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20,
            'detail_info' => 'Número de Identificación Tributaria (opcional)',
            'pattern' => '^[0-9-]+$',
            'error_messages' => [
                'min_length' => 'El NIT debe tener al menos 5 dígitos',
                'max_length' => 'El NIT no puede exceder 20 caracteres',
                'pattern' => 'Solo se permiten números y guiones'
            ]
        ]
    ],

    // Grupo 2: Información de la Relación
    [
        'formulario_id' => 3,
        'name' => 'comper',
        'type' => 'text',
        'label' => '¿Es compañer@ permanente?',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 1,
        'default_value' => 'N',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Indique si es una unión marital de hecho',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 1,
            'max_length' => 1,
            'field_size' => 1,
            'detail_info' => 'Indica si es una unión marital de hecho',
            'error_messages' => [
                'required' => 'Debe indicar si es compañero/a permanente',
                'min_length' => 'Seleccione una opción válida',
                'max_length' => 'Solo se permite un carácter (S/N)'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'tiecon',
        'type' => 'number',
        'label' => 'Tiempo de Convivencia (años)',
        'placeholder' => 'Ingrese los años de convivencia',
        'form_type' => 'number',
        'group_id' => 2,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Tiempo que llevan viviendo juntos',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 0,
            'max_length' => 100,
            'field_size' => 3,
            'detail_info' => 'Tiempo de convivencia en años con el afiliado',
            'pattern' => '^[0-9]+$',
            'error_messages' => [
                'required' => 'El tiempo de convivencia es obligatorio',
                'min_length' => 'El valor mínimo es 0',
                'max_length' => 'El valor máximo es 100',
                'pattern' => 'Solo se permiten números enteros'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'autoriza',
        'type' => 'text',
        'label' => 'Autoriza tratamiento de datos',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 3,
        'default_value' => 'S',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'S', 'label' => 'Sí'],
            ['value' => 'N', 'label' => 'No']
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
    ],

    // Grupo 3: Datos Personales del Cónyuge
    [
        'formulario_id' => 3,
        'name' => 'tipdoc',
        'type' => 'text',
        'label' => 'Tipo de Documento',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 1,
        'default_value' => 'CC',
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
            'error_messages' => [
                'required' => 'El tipo de documento es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'cedcon',
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
            'error_messages' => [
                'required' => 'El número de documento es obligatorio',
                'min_length' => 'El documento debe tener al menos 5 dígitos',
                'max_length' => 'El documento no puede exceder 20 dígitos',
                'pattern' => 'Solo se permiten números'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
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
        'help_text' => 'Ingrese el primer nombre del cónyuge',
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
        'formulario_id' => 3,
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
        'formulario_id' => 3,
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
        'help_text' => 'Ingrese el primer apellido del cónyuge',
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
        'formulario_id' => 3,
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
        'formulario_id' => 3,
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
        'help_text' => 'Seleccione la fecha de nacimiento del cónyuge',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de nacimiento es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
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
        'help_text' => 'Seleccione el sexo del cónyuge',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El sexo es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'estciv',
        'type' => 'text',
        'label' => 'Estado Civil',
        'placeholder' => 'Seleccione el estado civil',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 9,
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
        'help_text' => 'Seleccione el estado civil del cónyuge',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El estado civil es obligatorio'
            ]
        ]
    ],

    // Grupo 4: Información de Contacto
    [
        'formulario_id' => 3,
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
            'pattern' => '^[0-9]{7,15}$',
            'error_messages' => [
                'pattern' => 'Ingrese un número de teléfono válido (solo números, 7-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
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
            'pattern' => '^[0-9]{10,15}$',
            'error_messages' => [
                'required' => 'El celular es obligatorio',
                'pattern' => 'Ingrese un número de celular válido (solo números, 10-15 dígitos)'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
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
        'formulario_id' => 3,
        'name' => 'ciures',
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
        'formulario_id' => 3,
        'name' => 'codzon',
        'type' => 'text',
        'label' => 'Zona',
        'placeholder' => 'Seleccione la zona',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'URBANA', 'label' => 'Urbana'],
            ['value' => 'RURAL', 'label' => 'Rural']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la zona de residencia',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La zona es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'tipviv',
        'type' => 'text',
        'label' => 'Tipo de Vivienda',
        'placeholder' => 'Seleccione el tipo de vivienda',
        'form_type' => 'select',
        'group_id' => 5,
        'order' => 3,
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
        'formulario_id' => 3,
        'name' => 'direccion',
        'type' => 'text',
        'label' => 'Dirección de Residencia',
        'placeholder' => 'Ingrese la dirección completa',
        'form_type' => 'input',
        'group_id' => 5,
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

    // Grupo 6: Información Educativa y Laboral
    [
        'formulario_id' => 3,
        'name' => 'nivedu',
        'type' => 'text',
        'label' => 'Nivel Educativo',
        'placeholder' => 'Seleccione el nivel educativo',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'PRIMARIA', 'label' => 'Primaria'],
            ['value' => 'SECUNDARIA', 'label' => 'Secundaria'],
            ['value' => 'TECNICO', 'label' => 'Técnico'],
            ['value' => 'TECNOLOGO', 'label' => 'Tecnólogo'],
            ['value' => 'UNIVERSITARIO', 'label' => 'Universitario'],
            ['value' => 'POSGRADO', 'label' => 'Posgrado'],
            ['value' => 'NINGUNO', 'label' => 'Ninguno']
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
        'formulario_id' => 3,
        'name' => 'captra',
        'type' => 'text',
        'label' => 'Capacidad de Trabajo',
        'placeholder' => 'Seleccione la capacidad',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 2,
        'default_value' => 'SI',
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
        'formulario_id' => 3,
        'name' => 'tipdis',
        'type' => 'text',
        'label' => 'Tipo de Discapacidad',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 3,
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
        'formulario_id' => 3,
        'name' => 'codocu',
        'type' => 'text',
        'label' => 'Ocupación',
        'placeholder' => 'Seleccione la ocupación',
        'form_type' => 'select',
        'group_id' => 6,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [], // Se llenará dinámicamente
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la ocupación principal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La ocupación es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'empresalab',
        'type' => 'text',
        'label' => 'Empresa donde labora',
        'placeholder' => 'Ingrese el nombre de la empresa',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Nombre de la empresa donde trabaja actualmente',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 200,
            'error_messages' => [
                'max_length' => 'El nombre de la empresa no puede exceder 200 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'fecing',
        'type' => 'date',
        'label' => 'Fecha de Inicio Laboral',
        'placeholder' => 'Seleccione la fecha',
        'form_type' => 'date',
        'group_id' => 6,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Fecha en que inició a laborar en la empresa actual',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'salario',
        'type' => 'number',
        'label' => 'Ingresos Mensuales',
        'placeholder' => 'Ingrese el valor numérico',
        'form_type' => 'number',
        'group_id' => 6,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el monto de los ingresos mensuales',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 0,
            'error_messages' => [
                'min_length' => 'El valor no puede ser negativo'
            ]
        ]
    ],

    // Grupo 7: Información Bancaria
    [
        'formulario_id' => 3,
        'name' => 'tippag',
        'type' => 'text',
        'label' => 'Tipo de Pago de Subsidio',
        'placeholder' => 'Seleccione el tipo de pago',
        'form_type' => 'select',
        'group_id' => 7,
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
        'help_text' => 'Seleccione cómo desea recibir los subsidios',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de pago es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 3,
        'name' => 'numcue',
        'type' => 'text',
        'label' => 'Número de Cuenta',
        'placeholder' => 'Ingrese el número de cuenta',
        'form_type' => 'input',
        'group_id' => 7,
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
        'formulario_id' => 3,
        'name' => 'codban',
        'type' => 'text',
        'label' => 'Banco',
        'placeholder' => 'Seleccione el banco',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 3,
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

    // Grupo 8: Información Adicional
    [
        'formulario_id' => 3,
        'name' => 'peretn',
        'type' => 'text',
        'label' => 'Pertenencia Étnica',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 1,
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
        'formulario_id' => 3,
        'name' => 'resguardo_id',
        'type' => 'text',
        'label' => 'Resguardo Indígena',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 2,
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
        'formulario_id' => 3,
        'name' => 'pub_indigena_id',
        'type' => 'text',
        'label' => 'Pueblo Indígena',
        'placeholder' => 'Seleccione si aplica',
        'form_type' => 'select',
        'group_id' => 8,
        'order' => 3,
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
    ]
];
