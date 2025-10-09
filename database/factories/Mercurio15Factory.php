<?php

namespace Database\Factories;

use App\Models\Mercurio15;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio15.
 * Genera datos de prueba para la tabla mercurio15.
 */
class Mercurio15Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio15::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'estado' => $this->faker->optional()->randomElement(['Activo', 'Inactivo', 'Pendiente']), // Ejemplos para estado (char 8)
            'detalle' => $this->faker->optional()->sentence(20), // Texto opcional de hasta 20 palabras
            'tipo_rural' => $this->faker->randomElement(['S', 'N']), // 'S' o 'N' para tipo_rural
        ];
    }
}
