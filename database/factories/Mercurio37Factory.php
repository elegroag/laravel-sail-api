<?php

namespace Database\Factories;

use App\Models\Mercurio37;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio37.
 * Genera datos de prueba para la tabla mercurio37.
 */
class Mercurio37Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio37::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipopc' => $this->faker->randomElement(['01', '02']),
            'numero' => $this->faker->unique()->randomNumber(5, true),
            'coddoc' => $this->faker->unique()->randomNumber(5, true),
            'archivo' => $this->faker->filePath(),
            'fhash' => $this->faker->optional()->sha256(),
        ];
    }
}