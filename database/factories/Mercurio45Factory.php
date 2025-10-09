<?php

namespace Database\Factories;

use App\Models\Mercurio45;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio45.
 * Genera datos de prueba para la tabla mercurio45.
 */
class Mercurio45Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio45::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'cedtra' => $this->faker->numerify('###############'),
            'codben' => $this->faker->randomNumber(5, true),
            'nombre' => $this->faker->name(),
            'fecha' => $this->faker->date(),
            'codcer' => $this->faker->numerify('####'),
            'nomcer' => $this->faker->text(45),
            'archivo' => $this->faker->text(100),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'motivo' => $this->faker->optional()->text(100),
            'fecest' => $this->faker->optional()->date(),
            'codest' => $this->faker->optional()->numerify('##'),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
        ];
    }
}
