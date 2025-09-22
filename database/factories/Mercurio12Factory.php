<?php

namespace Database\Factories;

use App\Models\Mercurio12;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio12.
 * Genera datos de prueba para la tabla mercurio12.
 */
class Mercurio12Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio12::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'coddoc' => $this->faker->unique()->randomNumber(5, true), // Genera un número único para coddoc
            'detalle' => $this->faker->text(60), // Genera texto aleatorio de hasta 60 caracteres
        ];
    }
}