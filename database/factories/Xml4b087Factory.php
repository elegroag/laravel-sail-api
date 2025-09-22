<?php

namespace Database\Factories;

use App\Models\Xml4b087;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b087.
 * Genera datos de prueba para la tabla xml4b087.
 */
class Xml4b087Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b087::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codpob' => $this->faker->unique()->randomNumber(5, true),
            'nombre' => $this->faker->text(100),
        ];
    }
}