<?php

namespace Database\Factories;

use App\Models\Mercurio61;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio61.
 * Genera datos de prueba para la tabla mercurio61.
 */
class Mercurio61Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio61::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'numero' => $this->faker->randomNumber(5, true),
            'item' => $this->faker->unique()->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(15),
            'cantidad' => $this->faker->numberBetween(1, 100),
            'valor' => $this->faker->numberBetween(100, 10000),
        ];
    }
}