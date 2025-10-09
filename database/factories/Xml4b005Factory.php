<?php

namespace Database\Factories;

use App\Models\Xml4b005;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b005.
 * Genera datos de prueba para la tabla xml4b005.
 */
class Xml4b005Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b005::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipgen' => $this->faker->unique()->randomNumber(2, true),
            'nombre' => $this->faker->text(45),
            'codsex' => $this->faker->optional()->randomElement(['M', 'F']),
        ];
    }
}
