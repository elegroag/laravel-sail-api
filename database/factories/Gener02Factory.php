<?php

namespace Database\Factories;

use App\Models\Gener21;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gener02>
 */
class Gener02Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accesos = ['R', 'H'];
        $estados = ['A', 'I', 'B'];

        return [
            // PK no autoincremental
            'usuario' => fake()->unique()->numberBetween(1000, 9999999),

            'nombre' => fake()->name(),

            // FK a gener21.tipfun (char(4))
            'tipfun' => Gener21::factory()->create()->tipfun,

            'email' => fake()->optional(0.6)->safeEmail(),
            'login' => fake()->optional(0.6)->userName(),
            'criptada' => fake()->optional(0.5)->sha256(),
            'acceso' => fake()->randomElement($accesos),
            'estado' => fake()->randomElement($estados),
            'cedtra' => fake()->optional(0.5)->numberBetween(1_000_000, 9_999_999_999),
            'clave' => fake()->optional(0.5)->password(8, 20),
        ];
    }
}
