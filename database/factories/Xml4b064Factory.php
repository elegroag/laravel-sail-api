<?php

namespace Database\Factories;

use App\Models\Xml4b064;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Xml4b064.
 * Genera datos de prueba para la tabla xml4b064.
 */
class Xml4b064Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Xml4b064::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codare' => $this->faker->unique()->randomNumber(5, true),
            'nombre' => $this->faker->text(20),
        ];
    }
}
