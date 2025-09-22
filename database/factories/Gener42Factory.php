<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gener42>
 */
class Gener42Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // usuario: entero (int) válido
        $usuario = $this->faker->numberBetween(1, 700);

        // permiso: CHAR(10), aquí usamos un número en texto (1..150) como en el dataset
        $permiso = (string) $this->faker->numberBetween(1, 150);

        return [
            'usuario' => $usuario,
            'permiso' => $permiso, // <= 10 chars
        ];
    }
}
