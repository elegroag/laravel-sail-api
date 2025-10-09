<?php

namespace Database\Factories;

use App\Models\Xml4b081;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b081.
 * Genera datos de prueba para la tabla xml4b081.
 */
class Xml4b081Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b081::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipben' => $this->faker->unique()->randomNumber(2, true),
            'nombre' => $this->faker->text(20),
        ];
    }
}
