<?php

namespace Database\Factories;

use App\Models\Mercurio68;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio68.
 * Genera datos de prueba para la tabla mercurio68.
 */
class Mercurio68Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio68::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'tipben' => $this->faker->randomElement(['01', '02']),
            'docben' => $this->faker->text(7),
            'codben' => $this->faker->randomElement(['01', '02']),
            'valor' => $this->faker->numberBetween(100, 10000),
            'email' => $this->faker->email(),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
        ];
    }
}