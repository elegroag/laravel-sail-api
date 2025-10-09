<?php

namespace Database\Factories;

use App\Models\Mercurio28;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio28.
 * Genera datos de prueba para la tabla mercurio28.
 */
class Mercurio28Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio28::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02', '03']), // Ejemplos para tipo
            'campo' => $this->faker->text(20), // Texto para campo
            'detalle' => $this->faker->text(45), // Texto para detalle
            'orden' => $this->faker->numberBetween(1, 100), // Orden numérico
        ];
    }
}
