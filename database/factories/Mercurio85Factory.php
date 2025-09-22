<?php

namespace Database\Factories;

use App\Models\Mercurio85;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio85.
 * Genera datos de prueba para la tabla mercurio85.
 */
class Mercurio85Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio85::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(5, true),
            'tipideacu' => $this->faker->randomNumber(5, true),
            'numideacu' => $this->faker->text(15),
            'prinomacu' => $this->faker->firstName(),
            'segnomacu' => $this->faker->optional()->firstName(),
            'priapeacu' => $this->faker->lastName(),
            'segapeacu' => $this->faker->optional()->lastName(),
            'telacu' => $this->faker->optional()->phoneNumber(),
        ];
    }
}