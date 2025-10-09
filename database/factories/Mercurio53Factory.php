<?php

namespace Database\Factories;

use App\Models\Mercurio53;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio53.
 * Genera datos de prueba para la tabla mercurio53.
 */
class Mercurio53Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio53::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'numero' => $this->faker->unique()->randomNumber(5, true),
            'archivo' => $this->faker->filePath(),
            'orden' => $this->faker->numberBetween(1, 100),
            'url' => $this->faker->optional()->url(),
        ];
    }
}
