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
