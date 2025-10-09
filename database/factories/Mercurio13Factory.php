<?php

namespace Database\Factories;

use App\Models\Mercurio13;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio13.
 * Genera datos de prueba para la tabla mercurio13.
 */
class Mercurio13Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio13::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipopc' => $this->faker->randomElement(['01', '02', '03']), // Ejemplos de valores para tipopc (char 2)
            'coddoc' => $this->faker->unique()->randomNumber(5, true), // Número único para coddoc
            'obliga' => $this->faker->randomElement(['S', 'N']), // 'S' para sí, 'N' para no (char 1)
            'auto_generado' => $this->faker->numberBetween(0, 1), // 0 o 1 para tinyint
            'nota' => $this->faker->optional()->sentence(50), // Texto opcional de hasta 50 palabras
        ];
    }
}
