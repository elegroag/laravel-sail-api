<?php

namespace Database\Factories;

use App\Models\Mercurio70;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio70.
 * Genera datos de prueba para la tabla mercurio70.
 */
class Mercurio70Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio70::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'codser' => $this->faker->text(7),
            'puntos' => $this->faker->numberBetween(1, 1000),
            'calificacion' => $this->faker->numberBetween(1, 5),
            'nota' => $this->faker->optional()->text(255),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
        ];
    }
}
