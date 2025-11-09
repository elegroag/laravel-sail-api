<?php

namespace Database\Seeders;

use App\Models\ComponenteDinamico;
use App\Models\ComponenteValidacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponenteValidacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener componentes existentes y crear validaciones adicionales si es necesario
        $componentes = ComponenteDinamico::all();

        foreach ($componentes as $componente) {
            // Si ya tiene validación, saltar
            if ($componente->validacion) {
                continue;
            }

            // Crear validaciones por defecto basadas en el tipo
            $validacionData = $this->getValidacionPorTipo($componente->type);

            if ($validacionData) {
                $componente->validacion()->create($validacionData);
            }
        }

        // Crear validaciones adicionales de ejemplo
        $validacionesAdicionales = [
            [
                'componente_id' => 1, // Asumiendo que existe el componente con ID 1
                'pattern' => '/^[A-Z][a-z]+\s[A-Z][a-z]+$/',
                'max_length' => 50,
                'is_required' => true,
                'custom_rules' => [
                    'capitalized' => true
                ],
                'error_messages' => [
                    'pattern' => 'El nombre debe empezar con mayúscula y tener nombre y apellido',
                    'max_length' => 'El nombre no puede exceder 50 caracteres',
                    'required' => 'El nombre es obligatorio'
                ]
            ],
            [
                'componente_id' => 2, // Email
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'is_required' => true,
                'error_messages' => [
                    'pattern' => 'Formato de email inválido',
                    'required' => 'El email es obligatorio'
                ]
            ]
        ];

        foreach ($validacionesAdicionales as $validacion) {
            // Solo crear si el componente existe
            if (ComponenteDinamico::find($validacion['componente_id'])) {
                ComponenteValidacion::create($validacion);
            }
        }
    }

    /**
     * Obtener validación por defecto según el tipo de componente
     */
    private function getValidacionPorTipo(string $type): ?array
    {
        return match ($type) {
            'input' => [
                'pattern' => '/^[a-zA-Z0-9\s\-_.]+$/',
                'max_length' => 255,
                'is_required' => false,
                'error_messages' => [
                    'pattern' => 'Solo se permiten letras, números, espacios y caracteres básicos',
                    'max_length' => 'El texto no puede exceder 255 caracteres'
                ]
            ],
            'textarea' => [
                'max_length' => 1000,
                'is_required' => false,
                'error_messages' => [
                    'max_length' => 'El texto no puede exceder 1000 caracteres'
                ]
            ],
            'select' => [
                'is_required' => false,
                'error_messages' => [
                    'required' => 'Debe seleccionar una opción'
                ]
            ],
            'date' => [
                'is_required' => false,
                'error_messages' => [
                    'required' => 'La fecha es obligatoria'
                ]
            ],
            'number' => [
                'pattern' => '/^\d+(\.\d{1,2})?$/',
                'is_required' => false,
                'error_messages' => [
                    'pattern' => 'Debe ser un número válido con máximo 2 decimales',
                    'required' => 'El número es obligatorio'
                ]
            ],
            'dialog' => [
                'is_required' => false,
                'error_messages' => [
                    'required' => 'Debe confirmar el diálogo'
                ]
            ],
            default => null
        };
    }
}
