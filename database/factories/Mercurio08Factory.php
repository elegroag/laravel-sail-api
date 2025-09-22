<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio08>
 */
class Mercurio08Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // codofi: 2 dígitos (ej. '01')
        $codofi = str_pad((string) $this->faker->numberBetween(1, 99), 2, '0', STR_PAD_LEFT);
        // tipopc: 2 letras mayúsculas (coincidirá con mercurio09.tipopc en tu dataset real)
        $tipopc = strtoupper($this->faker->lexify('??'));
        // usuario: id entero (coincidirá con gener02.usuario en tu dataset real)
        $usuario = $this->faker->numberBetween(1, 700);
        // orden: entero de ordenamiento
        $orden = $this->faker->numberBetween(1, 1000);

        return [
            'codofi' => $codofi,
            'tipopc' => $tipopc,
            'usuario' => $usuario,
            'orden' => $orden,
        ];
    }

    /**
     * Estado para fijar una oficina existente.
     */
    public function forOficina(string $codofi): self
    {
        return $this->state(fn () => ['codofi' => substr($codofi, 0, 2)]);
    }
}
