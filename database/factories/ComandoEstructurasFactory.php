<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComandoEstructuras>
 */
class ComandoEstructurasFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $procesadores = ['php', 'p7', 'py', 'javac', 'npm'];
        $tipos = ['batch', 'cron', 'queue', 'script', 'job'];
        $sistemas = ['linux', 'windows', 'macos'];
        $entornos = ['D', 'T', 'P']; // Desarrollo, Test, Producción

        return [
            'procesador' => fake()->randomElement($procesadores),
            'estructura' => fake()->slug(),
            'variables' => fake()->optional(0.6)->words(3, true),
            'tipo' => fake()->optional(0.7)->randomElement($tipos),
            'sistema' => fake()->optional(0.7)->randomElement($sistemas),
            'env' => fake()->optional(0.7)->randomElement($entornos),
            'descripcion' => fake()->optional(0.6)->sentence(),
            'asyncro' => fake()->boolean() ? 1 : 0,
        ];
    }
}
