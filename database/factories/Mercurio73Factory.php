<?php

namespace Database\Factories;

use App\Models\Mercurio73;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio73.
 * Genera datos de prueba para la tabla mercurio73.
 */
class Mercurio73Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio73::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'archivo' => $this->faker->filePath(),
            'orden' => $this->faker->numberBetween(1, 100),
            'url' => $this->faker->optional()->url(),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}