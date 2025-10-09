<?php

namespace Database\Factories;

use App\Models\Mercusat02;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercusat02.
 * Genera datos de prueba para la tabla mercusat02.
 */
class Mercusat02Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercusat02::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'fecsol' => $this->faker->optional()->date('Y-m-d'),
            'fecapr' => $this->faker->optional()->date('Y-m-d'),
            'indeti' => $this->faker->optional()->numerify('###############'),
            'numtrasat' => $this->faker->optional()->text(18),
            'documento' => $this->faker->optional()->text(16),
            'coddoc' => $this->faker->optional()->randomElement(['01', '02']),
        ];
    }
}
