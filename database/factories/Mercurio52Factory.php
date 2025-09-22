<?php

namespace Database\Factories;

use App\Models\Mercurio52;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio52.
 * Genera datos de prueba para la tabla mercurio52.
 */
class Mercurio52Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio52::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codmen' => $this->faker->unique()->randomNumber(5, true),
            'detalle' => $this->faker->text(45),
            'codare' => $this->faker->randomNumber(5, true),
            'url' => $this->faker->url(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}