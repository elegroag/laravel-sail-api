<?php

namespace Database\Factories;

use App\Models\Mercurio64;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio64.
 * Genera datos de prueba para la tabla mercurio64.
 */
class Mercurio64Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio64::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'tipmov' => $this->faker->randomElement(['S', 'U', 'T']),
            'pergir' => $this->faker->optional()->text(6),
            'online' => $this->faker->optional()->randomNumber(5, true),
            'transferencia' => $this->faker->optional()->randomNumber(5, true),
            'valor' => $this->faker->numberBetween(100, 10000),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
            'estado' => $this->faker->randomElement(['A', 'I']),
        ];
    }
}