<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gener18>
 */
class Gener18Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // PK CHAR(2)
            'coddoc' => strtoupper($this->faker->unique()->bothify('??')),
            // CHAR(120)
            'detdoc' => strtoupper(substr($this->faker->sentence(3), 0, 120)),
            // CHAR(4)
            'codrua' => strtoupper($this->faker->bothify('??##')),
        ];
    }
}
