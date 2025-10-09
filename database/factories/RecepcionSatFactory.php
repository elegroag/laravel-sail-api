<?php

namespace Database\Factories;

use App\Models\RecepcionSat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo RecepcionSat.
 * Genera datos de prueba para la tabla recepcionsat.
 */
class RecepcionSatFactory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = RecepcionSat::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'contenido' => $this->faker->text(1000),
            'numero_transaccion' => $this->faker->text(200),
            'fecha' => $this->faker->dateTime(),
        ];
    }
}
