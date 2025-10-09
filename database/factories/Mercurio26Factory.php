<?php

namespace Database\Factories;

use App\Models\Mercurio26;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio26.
 * Genera datos de prueba para la tabla mercurio26.
 */
class Mercurio26Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio26::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'numero' => $this->faker->unique()->randomNumber(5, true), // Número único para numero
            'archivo' => $this->faker->text(45), // Texto para archivo
            'orden' => $this->faker->numberBetween(1, 100), // Orden numérico
            'tipo' => $this->faker->optional()->randomElement(['F', 'V']), // 'F' o 'V' opcional
        ];
    }
}
