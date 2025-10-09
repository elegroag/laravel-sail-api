<?php

namespace Database\Factories;

use App\Models\Mercurio33;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio33.
 * Genera datos de prueba para la tabla mercurio33.
 */
class Mercurio33Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio33::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('##############'),
            'campo' => $this->faker->text(20),
            'antval' => $this->faker->text(45),
            'valor' => $this->faker->text(100),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'motivo' => $this->faker->optional()->text(100),
            'fecest' => $this->faker->optional()->date(),
            'codest' => $this->faker->optional()->numerify('##'),
            'actualizacion' => $this->faker->numberBetween(0, 100),
        ];
    }
}
