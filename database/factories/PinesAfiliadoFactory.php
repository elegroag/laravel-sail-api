<?php

namespace Database\Factories;

use App\Models\PinesAfiliado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo PinesAfiliado.
 * Genera datos de prueba para la tabla pines_afiliado.
 */
class PinesAfiliadoFactory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = PinesAfiliado::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'pin' => $this->faker->unique()->randomNumber(4, true),
            'fecha' => $this->faker->optional()->date(),
            'cedtra' => $this->faker->randomNumber(10, true),
            'docben' => $this->faker->randomNumber(10, true),
            'estado' => $this->faker->optional()->randomElement(['A', 'I', 'R']),
            'codser' => $this->faker->text(20),
            'fecres' => $this->faker->optional()->date(),
            'horres' => $this->faker->optional()->time('H:i:s'),
            'fecent' => $this->faker->optional()->date(),
            'horent' => $this->faker->optional()->time('H:i:s'),
            'medio' => $this->faker->optional()->randomElement(['A', 'E', 'P']),
            'userent' => $this->faker->optional()->text(4),
        ];
    }
}
