<?php

namespace Database\Factories;

use App\Models\Mercurio59;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio59.
 * Genera datos de prueba para la tabla mercurio59.
 */
class Mercurio59Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio59::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codinf' => $this->faker->text(30),
            'codser' => $this->faker->text(7),
            'numero' => $this->faker->unique()->randomNumber(5, true),
            'archivo' => $this->faker->optional()->filePath(),
            'nota' => $this->faker->text(255),
            'email' => $this->faker->email(),
            'precan' => $this->faker->randomElement(['S', 'N']),
            'autser' => $this->faker->randomElement(['S', 'N']),
            'consumo' => $this->faker->optional()->randomElement(['S', 'N']),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'fecini' => $this->faker->optional()->date(),
            'fecfin' => $this->faker->optional()->date(),
        ];
    }
}