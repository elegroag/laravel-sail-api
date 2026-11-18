<?php

namespace Database\Factories;

use App\Models\Gener40;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gener40>
 */
class Gener40Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // codigo: exactamente 5 caracteres en mayúsculas (CHAR(5))
        $codigo = strtoupper($this->faker->unique()->bothify('?????'));

        // detalle: texto opcional (nullable), máximo 255 caracteres
        $detalle = $this->faker->optional()->sentence(3);
        if ($detalle !== null) {
            $detalle = mb_substr($detalle, 0, 255);
        }

        // orden: entero opcional (int(5) nullable)
        $orden = $this->faker->optional()->numberBetween(0, 99999);

        return [
            'codigo' => $codigo,
            'detalle' => $detalle,
            'controller' => $this->faker->optional()->randomElement(['Gener40Controller', 'Gener42Controller', 'MenuController']),
            'action' => $this->faker->optional()->randomElement(['index', 'store', 'update', 'destroy']),
            'orden' => $orden,
        ];
    }
}
