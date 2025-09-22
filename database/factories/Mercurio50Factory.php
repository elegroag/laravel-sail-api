<?php

namespace Database\Factories;

use App\Models\Mercurio50;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio50.
 * Genera datos de prueba para la tabla mercurio50.
 */
class Mercurio50Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio50::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codapl' => $this->faker->unique()->randomElement(['01', '02', '03']),
            'webser' => $this->faker->domainName(),
            'path' => $this->faker->filePath(),
            'urlonl' => $this->faker->optional()->url(),
            'puncom' => $this->faker->optional()->randomNumber(5, true),
        ];
    }
}