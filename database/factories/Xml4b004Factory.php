<?php

namespace Database\Factories;

use App\Models\Xml4b004;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b004.
 * Genera datos de prueba para la tabla xml4b004.
 */
class Xml4b004Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b004::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipide' => $this->faker->unique()->randomNumber(2, true),
            'nombre' => $this->faker->text(60),
            'coddoc' => $this->faker->optional()->randomNumber(2, true),
        ];
    }
}
