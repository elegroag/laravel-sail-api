<?php

namespace Database\Factories;

use App\Models\Mercurio63;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio63.
 * Genera datos de prueba para la tabla mercurio63.
 */
class Mercurio63Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio63::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'detalle' => $this->faker->text(45),
            'tipmov' => $this->faker->randomElement(['S', 'R']),
            'movimiento' => $this->faker->optional()->randomNumber(5, true),
            'valor' => $this->faker->numberBetween(100, 10000),
            'hora' => $this->faker->time('H:i:s'),
            'fecsis' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}