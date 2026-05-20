<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'tipfun' => fake()->randomElement(['ADAD', 'SAPO', 'SAFI', 'ACTU', 'FOSF', 'UXML', 'UIS', 'INVI', 'CCAR', 'CRED', 'CONS', 'FONL', 'SLIQ', 'SAT']),
            'email' => fake()->unique()->safeEmail(),
            'login' => (string) fake()->unique()->randomNumber(5),
            'criptada' => Hash::make('password'),
            'acceso' => fake()->randomElement(['H', 'L', 'A']),
            'estado' => fake()->randomElement(['A', 'I']),
            'cedtra' => fake()->unique()->randomNumber(9),
            'clave' => null,
            'intentos' => 0,
        ];
    }

    /**
     * Indica que el usuario está activo.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'A',
        ]);
    }

    /**
     * Indica que el usuario está inactivo.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'I',
        ]);
    }

    /**
     * Indica que el email no ha sido verificado.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}