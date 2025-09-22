<?php

namespace Database\Factories;

use App\Models\Mercurio19;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio19.
 * Genera datos de prueba para la tabla mercurio19.
 */
class Mercurio19Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio19::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02', '03']), // Ejemplos para tipo
            'coddoc' => $this->faker->randomElement(['01', '02']), // Ejemplos para coddoc
            'documento' => $this->faker->numerify('################'), // 16 dígitos para documento
            'codigo' => $this->faker->randomElement(['A', 'B', 'C']), // Ejemplos para codigo
            'respuesta' => $this->faker->optional()->text(60), // Texto opcional para respuesta
            'codver' => $this->faker->optional()->numerify('##########'), // 10 dígitos para codver
            'token' => $this->faker->optional()->text(500), // Texto opcional para token
            'inicio' => $this->faker->optional()->dateTime(), // Fecha opcional
            'intentos' => $this->faker->numberBetween(0, 10), // Número para intentos
        ];
    }
}