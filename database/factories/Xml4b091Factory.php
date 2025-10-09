<?php

namespace Database\Factories;

use App\Models\Xml4b091;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b091.
 * Genera datos de prueba para la tabla xml4b091.
 */
class Xml4b091Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b091::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codpai' => $this->faker->unique()->randomNumber(3, true),
            'detpai' => $this->faker->country(),
        ];
    }
}
