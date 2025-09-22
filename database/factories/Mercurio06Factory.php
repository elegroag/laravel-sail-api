<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio06>
 */
class Mercurio06Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // PK tipo: exactamente 2 letras mayúsculas (CHAR(2))
        $tipo = strtoupper($this->faker->unique()->bothify('??'));
        // detalle: máx 40 caracteres
        $detalle = substr($this->faker->words(2, true), 0, 40);

        return [
            'tipo' => $tipo,
            'detalle' => $detalle,
        ];
    }
}
