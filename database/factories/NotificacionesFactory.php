<?php

namespace Database\Factories;

use App\Models\Notificaciones;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Notificaciones.
 * Genera datos de prueba para la tabla notificaciones.
 */
class NotificacionesFactory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Notificaciones::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->optional()->sentence(20),
            'descri' => $this->faker->optional()->paragraph(5),
            'user' => $this->faker->optional()->randomNumber(5, true),
            'estado' => $this->faker->optional()->randomElement(['A', 'I']),
            'progre' => $this->faker->optional()->numberBetween(0, 100),
            'result' => $this->faker->optional()->text(224),
            'dia' => $this->faker->optional()->date(),
            'hora' => $this->faker->optional()->time('H:i:s'),
        ];
    }
}