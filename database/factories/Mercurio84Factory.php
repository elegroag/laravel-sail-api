<?php

namespace Database\Factories;

use App\Models\Mercurio84;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio84.
 * Genera datos de prueba para la tabla mercurio84.
 */
class Mercurio84Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio84::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'evento' => $this->faker->unique()->randomNumber(5, true),
            'beneficiario' => $this->faker->unique()->randomNumber(5, true),
        ];
    }
}
