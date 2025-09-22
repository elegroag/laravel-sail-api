<?php

namespace Database\Factories;

use App\Models\Mercurio51;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio51.
 * Genera datos de prueba para la tabla mercurio51.
 */
class Mercurio51Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio51::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codcat' => $this->faker->unique()->randomNumber(5, true),
            'detalle' => $this->faker->text(45),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}