<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'rut' => fake()->unique()->bothify('########-#'),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'sector_economico' => fake()->randomElement(['Sector 1', 'Sector 2', 'Sector 3']),
            'numero_empleados' => fake()->numberBetween(1, 100),
            'descripcion' => fake()->text(),
            'estado' => fake()->randomElement(['activa', 'inactiva']),
        ];
    }
}
