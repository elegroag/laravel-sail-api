<?php

namespace Database\Factories;

use App\Models\Mercurio65;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio65.
 * Genera datos de prueba para la tabla mercurio65.
 */
class Mercurio65Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio65::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'nit' => $this->faker->numerify('###############'),
            'razsoc' => $this->faker->company(),
            'direccion' => $this->faker->address(),
            'email' => $this->faker->email(),
            'celular' => $this->faker->phoneNumber(),
            'codcla' => $this->faker->randomNumber(5, true),
            'detalle' => $this->faker->text(45),
            'archivo' => $this->faker->filePath(),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'lat' => $this->faker->optional()->latitude(),
            'log' => $this->faker->optional()->longitude(),
        ];
    }
}