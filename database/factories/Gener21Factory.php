<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gener21>
 */
class Gener21Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generar código tipfun como exactamente 4 letras mayúsculas (CHAR(4))
        $tipfun = strtoupper($this->faker->unique()->bothify('????'));

        return [
            'tipfun' => $tipfun,
            // Texto corto legible, <= 140 chars (CHAR(140) admite menos)
            'detalle' => ucwords(strtolower($this->faker->words(2, true))),
        ];
    }
}
