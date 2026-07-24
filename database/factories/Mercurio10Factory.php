<?php

namespace Database\Factories;

use App\Models\Mercurio10;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mercurio10>
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
        $estado = $this->faker->randomElement(['T', 'D', 'A', 'X', 'P']);
        $nota = substr($this->faker->paragraph(), 0, 800);
        $codest = $this->faker->optional()->randomElement(['01', '02', '03', '04']);
        $fecsis = $this->faker->date('Y-m-d');
        $camposCorregir = $this->faker->optional()->text(200);
        $ruuid = $estado === 'P'
            ? strtoupper($this->faker->lexify('???'))
                .'-'.now()->year
                .'-'.$this->faker->numerify('#####')
                .'-'.str_pad((string) $item, 2, '0', STR_PAD_LEFT)
            : null;

        return [
            'tipopc' => $tipopc,
            'numero' => $numero,
            'item' => $item,
            'estado' => $estado,
            'nota' => $nota,
            'codest' => $codest,
            'fecsis' => $fecsis,
            'campos_corregir' => $camposCorregir,
            'ruuid' => $ruuid,
            'cerrada' => $this->faker->randomElement(['S', 'N']),
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
