<?php

namespace Database\Factories;

use App\Models\Mercurio60;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio60.
 * Genera datos de prueba para la tabla mercurio60.
 */
class Mercurio60Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio60::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'codinf' => $this->faker->text(30),
            'codser' => $this->faker->text(7),
            'numero' => $this->faker->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->text(7),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'codcat' => $this->faker->randomElement(['A', 'B']),
            'valtot' => $this->faker->numberBetween(1000, 100000),
            'fecsis' => $this->faker->date(),
            'hora' => $this->faker->time('H:i:s'),
            'tipmov' => $this->faker->optional()->randomElement(['B', 'U', 'P']),
            'online' => $this->faker->optional()->randomNumber(5, true),
            'consumo' => $this->faker->randomElement(['S', 'N']),
            'feccon' => $this->faker->optional()->date(),
            'punuti' => $this->faker->optional()->randomNumber(5, true),
            'puntos' => $this->faker->optional()->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'P']),
        ];
    }
}
