<?php

namespace Database\Factories;

use App\Models\Mercurio46;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio46.
 * Genera datos de prueba para la tabla mercurio46.
 */
class Mercurio46Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio46::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'nit' => $this->faker->numerify('###############'),
            'fecsis' => $this->faker->date(),
            'archivo' => $this->faker->filePath(),
        ];
    }
}
