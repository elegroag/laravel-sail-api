<?php

namespace Database\Factories;

use App\Models\Mercurio80;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio80.
 * Genera datos de prueba para la tabla mercurio80.
 */
class Mercurio80Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio80::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'profesor' => $this->faker->randomNumber(5, true),
            'colegio' => $this->faker->randomNumber(5, true),
            'modain' => $this->faker->randomNumber(5, true),
            'modser' => $this->faker->randomNumber(5, true),
            'modjec' => $this->faker->randomNumber(5, true),
            'fecha' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}
