<?php

namespace Database\Factories;

use App\Models\Mercurio35;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio35.
 * Genera datos de prueba para la tabla mercurio35.
 */
class Mercurio35Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio35::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'nit' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'cedtra' => $this->faker->numerify('###############'),
            'nomtra' => $this->faker->name(),
            'codest' => $this->faker->numerify('####'),
            'fecret' => $this->faker->date(),
            'nota' => $this->faker->text(250),
            'usuario' => $this->faker->randomNumber(5, true),
            'archivo' => $this->faker->text(100),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'fecest' => $this->faker->optional()->date(),
            'motivo' => $this->faker->optional()->text(255),
            'motrec' => $this->faker->optional()->numerify('##'),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
        ];
    }
}