<?php

namespace Database\Factories;

use App\Models\Mercurio57;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio57.
 * Genera datos de prueba para la tabla mercurio57.
 */
class Mercurio57Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio57::class;

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
