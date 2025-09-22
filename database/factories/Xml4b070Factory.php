<?php

namespace Database\Factories;

use App\Models\Xml4b070;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b070.
 * Genera datos de prueba para la tabla xml4b070.
 */
class Xml4b070Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b070::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipjor' => $this->faker->unique()->randomNumber(2, true),
            'nombre' => $this->faker->text(20),
        ];
    }
}