<?php

namespace Database\Factories;

use App\Models\Mercurio47;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio47.
 * Genera datos de prueba para la tabla mercurio47.
 */
class Mercurio47Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio47::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'documento' => $this->faker->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'fecest' => $this->faker->date(),
            'fecsol' => $this->faker->date(),
            'tipact' => $this->faker->randomElement(['U', 'N']),
            'usuario' => $this->faker->optional()->randomNumber(5, true),
        ];
    }
}
