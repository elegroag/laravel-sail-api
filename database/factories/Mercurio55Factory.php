<?php

namespace Database\Factories;

use App\Models\Mercurio55;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio55.
 * Genera datos de prueba para la tabla mercurio55.
 */
class Mercurio55Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio55::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codare' => $this->faker->unique()->randomNumber(5, true),
            'detalle' => $this->faker->text(45),
            'codcat' => $this->faker->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}