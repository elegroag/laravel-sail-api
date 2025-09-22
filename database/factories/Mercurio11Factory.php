<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio11>
 */
class Mercurio11Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // codest: exactamente 2 caracteres (letras mayúsculas)
        $codest = strtoupper($this->faker->unique()->bothify('??'));
        // detalle: hasta 45 caracteres
        $detalle = substr($this->faker->words(3, true), 0, 45);

        return [
            'codest' => $codest,
            'detalle' => $detalle,
        ];
    }
}
