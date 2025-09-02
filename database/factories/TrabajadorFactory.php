<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trabajador>
 */
class TrabajadorFactory extends Factory
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
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'fecha_nacimiento' => fake()->date(),
            'genero' => fake()->randomElement(['masculino', 'femenino', 'otro']),
            'direccion' => fake()->address(),
            'cargo' => fake()->jobTitle(),
            'salario' => fake()->randomFloat(2, 100000, 1000000),
            'fecha_ingreso' => fake()->date(),
            'fecha_salida' => fake()->optional()->date(),
            'estado' => fake()->randomElement(['activo', 'inactivo', 'suspendido']),
            'empresa_id' => fake()->numberBetween(1, 100),
        ];
    }
}
