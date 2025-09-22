<?php

namespace Database\Factories;

use App\Models\Xml4b094;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b094.
 * Genera datos de prueba para la tabla xml4b094.
 */
class Xml4b094Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b094::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'facvul' => $this->faker->unique()->randomNumber(5, true),
            'nombre' => $this->faker->text(45),
        ];
    }
}