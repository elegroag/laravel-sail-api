<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio05>
 */
class Mercurio05Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // codofi: 2 caracteres (usamos 2 dígitos tipo '01')
        $codofi = str_pad((string) $this->faker->numberBetween(1, 99), 2, '0', STR_PAD_LEFT);
        // codciu: 5 dígitos (tipo '18001')
        $codciu = str_pad((string) $this->faker->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);

        return [
            'codofi' => $codofi,
            'codciu' => $codciu,
        ];
    }

    /**
     * Estado para forzar una oficina específica.
     */
    public function forOficina(string $codofi): self
    {
        return $this->state(fn () => ['codofi' => substr($codofi, 0, 2)]);
    }
}
