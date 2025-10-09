<?php

namespace Database\Factories;

use App\Models\Mercurio20;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio20.
 * Genera datos de prueba para la tabla mercurio20.
 */
class Mercurio20Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio20::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->optional()->randomElement(['01', '02']), // Opcional para tipo
            'coddoc' => $this->faker->optional()->randomElement(['01', '02']), // Opcional para coddoc
            'documento' => $this->faker->optional()->numerify('##############'), // 14 dígitos opcionales
            'ip' => $this->faker->ipv4(), // IP aleatoria
            'fecha' => $this->faker->date(), // Fecha aleatoria
            'hora' => $this->faker->time('H:i:s'), // Hora en formato HH:MM:SS
            'accion' => $this->faker->text(100), // Texto para accion
            'nota' => $this->faker->optional()->text(300), // Texto opcional para nota
        ];
    }
}
