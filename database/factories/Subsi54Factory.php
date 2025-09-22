<?php

namespace Database\Factories;

use App\Models\Subsi54;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Subsi54.
 * Genera datos de prueba para la tabla subsi54.
 */
class Subsi54Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Subsi54::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipsoc' => $this->faker->randomElement(['01', '02', '03']),
            'detalle' => $this->faker->text(40),
        ];
    }
}