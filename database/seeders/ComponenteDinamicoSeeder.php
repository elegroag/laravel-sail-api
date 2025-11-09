<?php

namespace Database\Seeders;

use App\Models\ComponenteDinamico;
use App\Models\ComponenteValidacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponenteDinamicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $componentes = [
            [
                'name' => 'nombre_completo',
                'type' => 'input',
                'label' => 'Nombre Completo',
                'placeholder' => 'Ingrese su nombre completo',
                'form_type' => 'input',
                'group_id' => 1,
                'order' => 1,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Ingrese su nombre y apellidos',
                'target' => -1,
                'validacion' => [
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'max_length' => 100,
                    'min_length' => 2,
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'El nombre completo es obligatorio',
                        'pattern' => 'Solo se permiten letras y espacios',
                        'min_length' => 'El nombre debe tener al menos 2 caracteres',
                        'max_length' => 'El nombre no puede exceder 100 caracteres'
                    ]
                ]
            ],
            [
                'name' => 'email',
                'type' => 'input',
                'label' => 'Correo Electrónico',
                'placeholder' => 'correo@ejemplo.com',
                'form_type' => 'input',
                'group_id' => 1,
                'order' => 2,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Ingrese un correo electrónico válido',
                'target' => -1,
                'validacion' => [
                    'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
                    'max_length' => 255,
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'El correo electrónico es obligatorio',
                        'pattern' => 'Ingrese un correo electrónico válido',
                        'max_length' => 'El correo no puede exceder 255 caracteres'
                    ]
                ]
            ],
            [
                'name' => 'tipo_documento',
                'type' => 'select',
                'label' => 'Tipo de Documento',
                'placeholder' => 'Seleccione un tipo',
                'form_type' => 'select',
                'group_id' => 1,
                'order' => 3,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'data_source' => [
                    ['value' => 'cc', 'label' => 'Cédula de Ciudadanía'],
                    ['value' => 'ce', 'label' => 'Cédula de Extranjería'],
                    ['value' => 'ti', 'label' => 'Tarjeta de Identidad'],
                    ['value' => 'pas', 'label' => 'Pasaporte']
                ],
                'css_classes' => 'form-select',
                'help_text' => 'Seleccione el tipo de documento de identidad',
                'target' => -1,
                'validacion' => [
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'Debe seleccionar un tipo de documento'
                    ]
                ]
            ],
            [
                'name' => 'numero_documento',
                'type' => 'input',
                'label' => 'Número de Documento',
                'placeholder' => 'Ingrese el número',
                'form_type' => 'input',
                'group_id' => 1,
                'order' => 4,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Ingrese el número de documento sin puntos ni comas',
                'target' => -1,
                'validacion' => [
                    'pattern' => '/^[0-9]+$/',
                    'max_length' => 20,
                    'min_length' => 5,
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'El número de documento es obligatorio',
                        'pattern' => 'Solo se permiten números',
                        'min_length' => 'El número debe tener al menos 5 dígitos',
                        'max_length' => 'El número no puede exceder 20 dígitos'
                    ]
                ]
            ],
            [
                'name' => 'fecha_nacimiento',
                'type' => 'date',
                'label' => 'Fecha de Nacimiento',
                'placeholder' => 'Seleccione la fecha',
                'form_type' => 'date',
                'group_id' => 1,
                'order' => 5,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Seleccione su fecha de nacimiento',
                'target' => -1,
                'date_max' => now()->subYears(18)->format('Y-m-d'),
                'validacion' => [
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'La fecha de nacimiento es obligatoria'
                    ]
                ]
            ],
            [
                'name' => 'telefono',
                'type' => 'input',
                'label' => 'Teléfono',
                'placeholder' => '3001234567',
                'form_type' => 'input',
                'group_id' => 1,
                'order' => 6,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Ingrese su número de teléfono móvil',
                'target' => -1,
                'validacion' => [
                    'pattern' => '/^[0-9]{10}$/',
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'El teléfono es obligatorio',
                        'pattern' => 'Ingrese un número de teléfono válido (10 dígitos)'
                    ]
                ]
            ],
            [
                'name' => 'salario',
                'type' => 'number',
                'label' => 'Salario Mensual',
                'placeholder' => '0.00',
                'form_type' => 'number',
                'group_id' => 1,
                'order' => 7,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Ingrese su salario mensual en pesos colombianos',
                'target' => -1,
                'number_min' => 0,
                'number_max' => 100000000,
                'number_step' => 0.01,
                'validacion' => [
                    'is_required' => true,
                    'error_messages' => [
                        'required' => 'El salario es obligatorio'
                    ]
                ]
            ],
            [
                'name' => 'comentarios',
                'type' => 'textarea',
                'label' => 'Comentarios Adicionales',
                'placeholder' => 'Ingrese cualquier comentario adicional...',
                'form_type' => 'textarea',
                'group_id' => 1,
                'order' => 8,
                'default_value' => '',
                'is_disabled' => false,
                'is_readonly' => false,
                'css_classes' => 'form-control',
                'help_text' => 'Comentarios opcionales sobre el formulario',
                'target' => -1,
                'validacion' => [
                    'max_length' => 500,
                    'error_messages' => [
                        'max_length' => 'Los comentarios no pueden exceder 500 caracteres'
                    ]
                ]
            ]
        ];

        foreach ($componentes as $componenteData) {
            $validacion = $componenteData['validacion'] ?? null;
            unset($componenteData['validacion']);

            $componente = ComponenteDinamico::create($componenteData);

            if ($validacion) {
                $componente->validacion()->create($validacion);
            }
        }
    }
}
