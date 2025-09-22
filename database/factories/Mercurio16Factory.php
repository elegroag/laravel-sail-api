<?php

namespace Database\Factories;

use App\Models\Mercurio16;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio16.
 * Genera datos de prueba para la tabla mercurio16.
 */
class Mercurio16Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio16::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'documento' => $this->faker->numerify('###############'), // 15 dígitos para documento
            'coddoc' => $this->faker->randomElement(['01', '02', '03']), // Ejemplos para coddoc
            'firma' => $this->faker->text(180), // Texto para firma
            'fecha' => $this->faker->date(), // Fecha aleatoria
            'keyprivate' => $this->faker->optional()->text(500), // Texto opcional para keyprivate
            'keypublic' => $this->faker->optional()->text(500), // Texto opcional para keypublic
            'password' => $this->faker->optional()->password(), // Contraseña opcional
        ];
    }
}