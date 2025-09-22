<?php

namespace Database\Factories;

use App\Models\Mercurio58;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio58.
 * Genera datos de prueba para la tabla mercurio58.
 */
class Mercurio58Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio58::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'archivo' => $this->faker->filePath(),
            'orden' => $this->faker->numberBetween(1, 100),
            'codare' => $this->faker->randomNumber(5, true),
        ];
    }
}