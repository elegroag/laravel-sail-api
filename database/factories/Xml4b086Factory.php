<?php

namespace Database\Factories;

use App\Models\Xml4b086;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b086.
 * Genera datos de prueba para la tabla xml4b086.
 */
class Xml4b086Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b086::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codgru' => $this->faker->unique()->randomNumber(5, true),
            'nombre' => $this->faker->text(70),
        ];
    }
}