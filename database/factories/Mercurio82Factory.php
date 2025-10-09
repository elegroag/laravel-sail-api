<?php

namespace Database\Factories;

use App\Models\Mercurio82;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio82.
 * Genera datos de prueba para la tabla mercurio82.
 */
class Mercurio82Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio82::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'direccion' => $this->faker->optional()->address(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}
