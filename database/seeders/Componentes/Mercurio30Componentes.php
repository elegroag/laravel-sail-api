<?php

namespace Database\Seeders\Componentes;

return [
    [
        'formulario_id' => 1,
        'name' => 'tipper',
        'type' => 'text',
        'label' => 'Tipo de Persona',
        'placeholder' => 'Seleccione el tipo de persona',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'N', 'label' => 'Persona Natural'],
            ['value' => 'J', 'label' => 'Persona Jurídica']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione si es persona natural o jurídica',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 1,
            'max_length' => 1,
            'error_messages' => [
                'required' => 'El tipo de persona es obligatorio',
                'min_length' => 'Debe seleccionar una opción',
                'max_length' => 'Solo se permite una opción'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'nit',
        'type' => 'number',
        'label' => 'NIT o Documento',
        'placeholder' => 'Ingrese el NIT o documento',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el NIT para persona jurídica o documento para persona natural',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'field_size' => 20, // Tamaño máximo del campo en la base de datos
            'detail_info' => 'Campo para NIT o documento de identidad',
            'error_messages' => [
                'required' => 'El NIT o documento es obligatorio',
                'min_length' => 'El NIT o documento debe tener al menos 5 caracteres',
                'max_length' => 'El NIT o documento no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'razsoc',
        'type' => 'text',
        'label' => 'Razón Social',
        'placeholder' => 'Ingrese la razón social',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la razón social o nombre completo si es persona natural',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 3,
            'max_length' => 255,
            'field_size' => 255, // Tamaño máximo en la base de datos
            'detail_info' => 'Nombre completo o razón social del cliente',
            'error_messages' => [
                'required' => 'La razón social es obligatoria',
                'min_length' => 'La razón social debe tener al menos 3 caracteres',
                'max_length' => 'La razón social no puede exceder 255 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'digver',
        'type' => 'number',
        'label' => 'Dígito de Verificación',
        'placeholder' => 'DV',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Dígito de verificación del NIT',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'min_length' => 1,
            'max_length' => 1,
            'field_size' => 2, // Espacio para el dígito y un posible signo
            'detail_info' => 'Dígito de verificación del NIT',
            'error_messages' => [
                'min_length' => 'El dígito de verificación debe tener 1 carácter',
                'max_length' => 'El dígito de verificación no puede tener más de 1 carácter',
                'numeric' => 'El dígito de verificación debe ser un número'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'dirpri',
        'type' => 'text',
        'label' => 'Dirección Comercial',
        'placeholder' => 'Ingrese la dirección comercial',
        'form_type' => 'textarea',
        'group_id' => 1,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la dirección completa de la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 10,
            'max_length' => 500,
            'error_messages' => [
                'required' => 'La dirección comercial es obligatoria',
                'min_length' => 'La dirección debe tener al menos 10 caracteres',
                'max_length' => 'La dirección no puede exceder 500 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'ciupri',
        'type' => 'text',
        'label' => 'Ciudad Comercial',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 1,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => null,
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la ciudad donde se encuentra la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La ciudad comercial es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'telpri',
        'type' => 'tel',
        'label' => 'Teléfono Comercial',
        'placeholder' => 'Ingrese el teléfono con indicativo',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el teléfono con indicativo de ciudad o país',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '/^[0-9+()\-\s]+$/',
            'min_length' => 7,
            'max_length' => 20,
            'error_messages' => [
                'required' => 'El teléfono comercial es obligatorio',
                'pattern' => 'Ingrese un número de teléfono válido',
                'min_length' => 'El teléfono debe tener al menos 7 dígitos',
                'max_length' => 'El teléfono no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'emailpri',
        'type' => 'email',
        'label' => 'Correo Electrónico Comercial',
        'placeholder' => 'correo@empresa.com',
        'form_type' => 'input',
        'group_id' => 1,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el correo electrónico comercial',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El correo electrónico comercial es obligatorio',
                'pattern' => 'Ingrese un correo electrónico válido',
                'max_length' => 'El correo no puede exceder 100 caracteres'
            ]
        ]
    ],
    // Campos adicionales para el formulario Mercurio30
    [
        'formulario_id' => 1,
        'name' => 'coddocrepleg',
        'type' => 'text',
        'label' => 'Tipo de Documento Repleg',
        'placeholder' => 'Seleccione el tipo de documento',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'CC', 'label' => 'Cédula de Ciudadanía'],
            ['value' => 'CE', 'label' => 'Cédula de Extranjería'],
            ['value' => 'PA', 'label' => 'Pasaporte'],
            ['value' => 'TMF', 'label' => 'Tarjeta de Movilidad Fronteriza'],
            ['value' => 'CD', 'label' => 'Carne Diplomatico'],
            ['value' => 'ISE', 'label' => 'Identificación Dada por la Secretaría de Educación'],
            ['value' => 'V', 'label' => 'VISA'],
            ['value' => 'PT', 'label' => 'Permiso Protección Temporal'],
            ['value' => 'NU', 'label' => 'NUIP'],
            ['value' => 'PEP', 'label' => 'Permiso Especial de Permanencia'],
            ['value' => 'CB', 'label' => 'Certificado Cabildo']
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de documento de la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'El tipo de documento es obligatorio'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'sigla',
        'type' => 'text',
        'label' => 'Sigla',
        'placeholder' => 'Ingrese la sigla de la empresa',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la sigla o abreviatura de la empresa',
        'target' => -1,
        'validacion' => [
            'max_length' => 20,
            'error_messages' => [
                'max_length' => 'La sigla no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'matmer',
        'type' => 'text',
        'label' => 'Matrícula Mercantil',
        'placeholder' => 'Ingrese la matrícula mercantil',
        'form_type' => 'input',
        'group_id' => 2,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de matrícula mercantil',
        'target' => -1,
        'validacion' => [
            'max_length' => 50,
            'error_messages' => [
                'max_length' => 'La matrícula mercantil no puede exceder 50 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'tipsoc',
        'type' => 'text',
        'label' => 'Tipo de Sociedad',
        'placeholder' => 'Seleccione el tipo de sociedad',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => null,
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de sociedad de la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'tipemp',
        'type' => 'text',
        'label' => 'Tipo de Empresa',
        'placeholder' => 'Seleccione el tipo de empresa',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => null,
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione el tipo de empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'codzon',
        'type' => 'text',
        'label' => 'Zona de Trabajo',
        'placeholder' => 'Seleccione la zona de trabajo',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => null,
        'search_type' => 'local',
        'search_endpoint' => null,
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la zona donde laboran los trabajadores',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'codact',
        'type' => 'text',
        'label' => 'Actividad Económica',
        'placeholder' => 'Seleccione la actividad económica',
        'form_type' => 'select',
        'group_id' => 2,
        'order' => 7,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => null,
        'search_type' => 'local',
        'search_endpoint' => null,
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la actividad económica principal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La actividad económica es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'fecini',
        'type' => 'date',
        'label' => 'Fecha de Inicio de Actividades',
        'placeholder' => 'Seleccione la fecha',
        'form_type' => 'date',
        'group_id' => 2,
        'order' => 8,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Seleccione la fecha de inicio de actividades',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de inicio de actividades es obligatoria'
            ]
        ]
    ],
    // Campos adicionales para el formulario Mercurio30 - Continuación
    [
        'formulario_id' => 1,
        'name' => 'tottra',
        'type' => 'number',
        'label' => 'Total de Trabajadores',
        'placeholder' => 'Ingrese el total de trabajadores',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 1,
        'default_value' => '0',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número total de trabajadores de la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 0,
            'max_length' => 6,
            'field_size' => 6,
            'detail_info' => 'Número total de trabajadores en la empresa',
            'pattern' => '^[0-9]+$',
            'numeric_range' => '0-999999',
            'error_messages' => [
                'required' => 'El total de trabajadores es obligatorio',
                'min_length' => 'El valor no puede ser negativo',
                'max_length' => 'El valor no puede exceder 6 dígitos',
                'pattern' => 'Solo se permiten números enteros',
                'numeric_range' => 'El valor debe ser un número entre 0 y 999,999'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'valnom',
        'type' => 'number',
        'label' => 'Valor Nómina',
        'placeholder' => 'Ingrese el valor de la nómina',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 2,
        'default_value' => '0',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el valor total de la nómina',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 0,
            'max_length' => 15,
            'field_size' => 15,
            'detail_info' => 'Valor total de la nómina de la empresa',
            'pattern' => '^[0-9]+$',
            'numeric_range' => '0-999999999999999',
            'error_messages' => [
                'required' => 'El valor de la nómina es obligatorio',
                'min_length' => 'El valor no puede ser negativo',
                'max_length' => 'El valor no puede exceder 15 dígitos',
                'pattern' => 'Solo se permiten números enteros',
                'numeric_range' => 'El valor debe ser un número positivo'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'codcaj',
        'type' => 'text',
        'label' => 'Caja de Compensación Anterior',
        'placeholder' => 'Seleccione la caja de compensación',
        'form_type' => 'select',
        'group_id' => 3,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            // Estos datos deberían venir de una tabla de cajas de compensación
            ['value' => 'CCF001', 'label' => 'Caja de Compensación Familiar Compensar'],
            ['value' => 'CCF002', 'label' => 'Caja de Compensación Familiar Cafam'],
            ['value' => 'CCF003', 'label' => 'Caja de Compensación Familiar Colsubsidio']
            // Agregar más cajas según sea necesario
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la caja de compensación a la que estaba afiliado anteriormente',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'error_messages' => []
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'celpri',
        'type' => 'tel',
        'label' => 'Celular Comercial',
        'placeholder' => 'Ingrese el número de celular con indicativo',
        'form_type' => 'input',
        'group_id' => 3,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de celular con indicativo de ciudad o país',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '/^[0-9+()\-\s]+$/',
            'min_length' => 10,
            'max_length' => 20,
            'error_messages' => [
                'pattern' => 'Ingrese un número de celular válido',
                'min_length' => 'El celular debe tener al menos 10 dígitos',
                'max_length' => 'El celular no puede exceder 20 caracteres'
            ]
        ]
    ],
    // Sección de Notificaciones
    [
        'formulario_id' => 1,
        'name' => 'codciu',
        'type' => 'text',
        'label' => 'Ciudad de Notificación',
        'placeholder' => 'Seleccione la ciudad',
        'form_type' => 'select',
        'group_id' => 4,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            // Estos datos deberían venir de una tabla de ciudades
            ['value' => '5001', 'label' => 'Bogotá D.C.'],
            ['value' => '5002', 'label' => 'Medellín'],
            ['value' => '5003', 'label' => 'Cali']
            // Agregar más ciudades según sea necesario
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la ciudad para notificaciones',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La ciudad de notificación es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'direccion',
        'type' => 'text',
        'label' => 'Dirección de Notificación',
        'placeholder' => 'Ingrese la dirección para notificaciones',
        'form_type' => 'textarea',
        'group_id' => 4,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese la dirección completa para notificaciones',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 10,
            'max_length' => 500,
            'error_messages' => [
                'required' => 'La dirección de notificación es obligatoria',
                'min_length' => 'La dirección debe tener al menos 10 caracteres',
                'max_length' => 'La dirección no puede exceder 500 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'telefono',
        'type' => 'tel',
        'label' => 'Teléfono de Notificación',
        'placeholder' => 'Ingrese el teléfono con indicativo',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el teléfono con indicativo de ciudad o país',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '/^[0-9+()\-\s]+$/',
            'min_length' => 7,
            'max_length' => 20,
            'error_messages' => [
                'required' => 'El teléfono de notificación es obligatorio',
                'pattern' => 'Ingrese un número de teléfono válido',
                'min_length' => 'El teléfono debe tener al menos 7 dígitos',
                'max_length' => 'El teléfono no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'email',
        'type' => 'email',
        'label' => 'Correo Electrónico de Notificación',
        'placeholder' => 'correo@notificacion.com',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el correo electrónico para notificaciones',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El correo electrónico de notificación es obligatorio',
                'pattern' => 'Ingrese un correo electrónico válido',
                'max_length' => 'El correo no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'fax',
        'type' => 'tel',
        'label' => 'Fax de Notificación',
        'placeholder' => 'Ingrese el número de fax',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de fax (opcional)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '/^[0-9+()\-\s]+$/',
            'max_length' => 20,
            'error_messages' => [
                'pattern' => 'Ingrese un número de fax válido',
                'max_length' => 'El fax no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'celular',
        'type' => 'tel',
        'label' => 'Celular de Notificación',
        'placeholder' => 'Ingrese el número de celular con indicativo',
        'form_type' => 'input',
        'group_id' => 4,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de celular con indicativo de ciudad o país',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'pattern' => '/^[0-9+()\-\s]+$/',
            'min_length' => 10,
            'max_length' => 20,
            'error_messages' => [
                'pattern' => 'Ingrese un número de celular válido',
                'min_length' => 'El celular debe tener al menos 10 dígitos',
                'max_length' => 'El celular no puede exceder 20 caracteres'
            ]
        ]
    ],
    // Sección de Representante Legal
    [
        'formulario_id' => 1,
        'name' => 'cedrep',
        'type' => 'number',
        'label' => 'Documento del Representante Legal',
        'placeholder' => 'Ingrese el número de documento',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el número de documento del representante legal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 5,
            'max_length' => 20,
            'error_messages' => [
                'required' => 'El documento del representante legal es obligatorio',
                'min_length' => 'El documento debe tener al menos 5 caracteres',
                'max_length' => 'El documento no puede exceder 20 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'repleg',
        'type' => 'text',
        'label' => 'Nombre Completo del Representante Legal',
        'placeholder' => 'Ingrese el nombre completo',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el nombre completo del representante legal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 3,
            'max_length' => 255,
            'error_messages' => [
                'required' => 'El nombre del representante legal es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder 255 caracteres'
            ]
        ]
    ],
    // Campos para persona natural (cuando el empleador es persona natural)
    [
        'formulario_id' => 1,
        'name' => 'priape',
        'type' => 'text',
        'label' => 'Primer Apellido',
        'placeholder' => 'Ingrese el primer apellido',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 1,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer apellido (solo para persona natural)',
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
        'formulario_id' => 1,
        'name' => 'segape',
        'type' => 'text',
        'label' => 'Segundo Apellido',
        'placeholder' => 'Ingrese el segundo apellido',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo apellido (opcional, solo para persona natural)',
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
        'formulario_id' => 1,
        'name' => 'prinom',
        'type' => 'text',
        'label' => 'Primer Nombre',
        'placeholder' => 'Ingrese el primer nombre',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el primer nombre (solo para persona natural)',
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
        'formulario_id' => 1,
        'name' => 'segnom',
        'type' => 'text',
        'label' => 'Segundo Nombre',
        'placeholder' => 'Ingrese el segundo nombre',
        'form_type' => 'input',
        'group_id' => 6,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Ingrese el segundo nombre (opcional, solo para persona natural)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    // Autorización de tratamiento de datos
    [
        'formulario_id' => 1,
        'name' => 'autoriza',
        'type' => 'text',
        'label' => 'Autorización de Tratamiento de Datos',
        'placeholder' => 'Seleccione una opción',
        'form_type' => 'select',
        'group_id' => 7,
        'order' => 1,
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
    ],
    // Datos detallados del representante legal
    [
        'formulario_id' => 1,
        'name' => 'priaperepleg',
        'type' => 'text',
        'label' => 'Primer Apellido Representante Legal',
        'placeholder' => 'Ingrese el primer apellido',
        'form_type' => 'input',
        'group_id' => 5, // Mismo grupo que otros datos del representante legal
        'order' => 3,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Primer apellido del representante legal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El primer apellido del representante legal es obligatorio',
                'min_length' => 'El apellido debe tener al menos 2 caracteres',
                'max_length' => 'El apellido no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'segaperepleg',
        'type' => 'text',
        'label' => 'Segundo Apellido Representante Legal',
        'placeholder' => 'Ingrese el segundo apellido',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 4,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Segundo apellido del representante legal (opcional)',
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
        'formulario_id' => 1,
        'name' => 'prinomrepleg',
        'type' => 'text',
        'label' => 'Primer Nombre Representante Legal',
        'placeholder' => 'Ingrese el primer nombre',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 5,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Primer nombre del representante legal',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'error_messages' => [
                'required' => 'El primer nombre del representante legal es obligatorio',
                'min_length' => 'El nombre debe tener al menos 2 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'segnomrepleg',
        'type' => 'text',
        'label' => 'Segundo Nombre Representante Legal',
        'placeholder' => 'Ingrese el segundo nombre',
        'form_type' => 'input',
        'group_id' => 5,
        'order' => 6,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'css_classes' => 'form-control',
        'help_text' => 'Segundo nombre del representante legal (opcional)',
        'target' => -1,
        'validacion' => [
            'is_required' => false,
            'max_length' => 100,
            'error_messages' => [
                'max_length' => 'El nombre no puede exceder 100 caracteres'
            ]
        ]
    ],

    // Campos del sistema (ocultos/proceso)
    [
        'formulario_id' => 1,
        'name' => 'fecsol',
        'type' => 'date',
        'label' => 'Fecha de Solicitud',
        'placeholder' => '',
        'form_type' => 'date',
        'group_id' => 8, // Grupo para campos del sistema
        'order' => 1,
        'default_value' => date('Y-m-d'), // Fecha actual por defecto
        'is_disabled' => true, // Deshabilitado para el usuario
        'is_readonly' => true, // Solo lectura
        'css_classes' => 'form-control bg-light',
        'help_text' => 'Fecha en que se realizó la solicitud',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La fecha de solicitud es obligatoria'
            ]
        ]
    ],
    [
        'formulario_id' => 1,
        'name' => 'calemp',
        'type' => 'text',
        'label' => 'Calidad de la Empresa',
        'placeholder' => 'Seleccione la calidad de la empresa',
        'form_type' => 'select',
        'group_id' => 8, // Grupo para campos del sistema
        'order' => 2,
        'default_value' => '',
        'is_disabled' => false,
        'is_readonly' => false,
        'data_source' => [
            ['value' => 'E', 'label' => 'Empresa'],
            ['value' => 'F', 'label' => 'Facultativo'],
        ],
        'css_classes' => 'form-select',
        'help_text' => 'Seleccione la calidad de la empresa',
        'target' => -1,
        'validacion' => [
            'is_required' => true,
            'error_messages' => [
                'required' => 'La calidad de la empresa es obligatoria'
            ]
        ]
    ]
];
