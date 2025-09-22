<?php

namespace Database\Factories;

use App\Models\Mercurio74;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio74.
 * Genera datos de prueba para la tabla mercurio74.
 */
class Mercurio74Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio74::class;

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