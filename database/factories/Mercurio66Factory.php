<?php

namespace Database\Factories;

use App\Models\Mercurio66;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio66.
 * Genera datos de prueba para la tabla mercurio66.
 */
class Mercurio66Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio66::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codsed' => $this->faker->randomNumber(5, true),
            'detalle' => $this->faker->text(100),
            'valor' => $this->faker->numberBetween(100, 10000),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
            'estado' => $this->faker->randomElement(['C', 'P']),
            'fecest' => $this->faker->optional()->date(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(15),
            'coddoc' => $this->faker->randomElement(['01', '02']),
        ];
    }
}
