<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio04>
 */
class Mercurio04Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $codofi = strtoupper($this->faker->unique()->bothify('??'));
        $detalle = substr($this->faker->words(2, true), 0, 45);
        $principal = $this->faker->randomElement(['S', 'N']);
        $estado = $this->faker->randomElement(['A', 'I']);

        return [
            'codofi' => $codofi,
            'detalle' => $detalle,
            'principal' => $principal,
            'estado' => $estado,
        ];
    }

    /**
     * Estado de oficina principal.
     */
    public function principal(): self
    {
        return $this->state(fn () => ['principal' => 'S']);
    }

    /**
     * Estado activo.
     */
    public function activa(): self
    {
        return $this->state(fn () => ['estado' => 'A']);
    }
}
