<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NucleofamiliarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'rut' => fake()->unique()->bothify('########-#'),
            'fecha_nacimiento' => fake()->date(),
            'genero' => fake()->randomElement(['masculino', 'femenino', 'otro']),
            'parentesco' => fake()->randomElement(['conyuge', 'hijo', 'hija', 'padre', 'madre', 'hermano', 'hermana', 'otro']),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'direccion' => fake()->address(),
            'estado_civil' => fake()->randomElement(['soltero', 'casado', 'divorciado', 'viudo', 'union_libre']),
            'ocupacion' => fake()->jobTitle(),
            'dependiente_economico' => fake()->boolean(),
            'trabajador_id' => fake()->numberBetween(1, 100),
        ];
    }
}
