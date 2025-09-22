<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio10>
 */
class Mercurio10Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // PK compuesta: tipopc(2), numero(int), item(int)
        $tipopc = strtoupper($this->faker->lexify('??'));
        $numero = $this->faker->numberBetween(1, 999999);
        $item = $this->faker->numberBetween(1, 99);

        // Otros campos
        $estado = $this->faker->randomElement(['T','D','A','X','P']);
        $nota = substr($this->faker->paragraph(), 0, 800);
        $codest = $this->faker->optional()->randomElement(['01', '02', '03', '04']);
        $fecsis = $this->faker->date('Y-m-d');
        $camposCorregir = $this->faker->optional()->text(200);

        return [
            'tipopc' => $tipopc,
            'numero' => $numero,
            'item' => $item,
            'estado' => $estado,
            'nota' => $nota,
            'codest' => $codest,
            'fecsis' => $fecsis,
            'campos_corregir' => $camposCorregir,
        ];
    }

    /**
     * Estado aprobado.
     */
    public function aprobado(): self
    {
        return $this->state(fn () => ['estado' => 'A']);
    }
}
