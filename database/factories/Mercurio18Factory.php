<?php

namespace Database\Factories;

use App\Models\Mercurio18;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio18.
 * Genera datos de prueba para la tabla mercurio18.
 */
class Mercurio18Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio18::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->randomElement(['01', '02', '03', '04']), // Ejemplos para codigo (char 2)
            'detalle' => $this->faker->text(60), // Texto para detalle
        ];
    }
}
