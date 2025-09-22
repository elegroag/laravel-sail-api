<?php

namespace Database\Factories;

use App\Models\Mercurio62;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio62.
 * Genera datos de prueba para la tabla mercurio62.
 */
class Mercurio62Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio62::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(15),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'salgir' => $this->faker->numberBetween(0, 10000),
            'salrec' => $this->faker->numberBetween(0, 10000),
            'consumo' => $this->faker->numberBetween(0, 10000),
            'puntos' => $this->faker->numberBetween(0, 10000),
            'punuti' => $this->faker->numberBetween(0, 10000),
        ];
    }
}