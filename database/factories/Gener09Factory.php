<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gener09>
 */
class Gener09Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // char(9) único
            'codzon' => strtoupper(fake()->unique()->bothify('??#####??')),
            // char(30)
            'detzon' => strtoupper(substr(fake()->words(2, true), 0, 30)),
        ];
    }
}
