<?php

namespace Database\Factories;

use App\Models\Tranoms;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Tranoms.
 * Genera datos de prueba para la tabla tranoms.
 */
class TranomsFactory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Tranoms::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'request' => $this->faker->randomNumber(10, true),
            'cedtra' => $this->faker->numerify('##################'),
            'nomtra' => $this->faker->name(),
            'apetra' => $this->faker->name(),
            'saltra' => $this->faker->numerify('####################'),
            'fectra' => $this->faker->date(),
            'cartra' => $this->faker->optional()->text(120),
        ];
    }
}
