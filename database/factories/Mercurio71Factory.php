<?php

namespace Database\Factories;

use App\Models\Mercurio71;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio71.
 * Genera datos de prueba para la tabla mercurio71.
 */
class Mercurio71Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio71::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'email' => $this->faker->email(),
            'estado' => $this->faker->randomElement(['P', 'A']),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
            'fecest' => $this->faker->optional()->date(),
            'codigo' => $this->faker->optional()->text(8),
        ];
    }
}