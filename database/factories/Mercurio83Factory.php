<?php

namespace Database\Factories;

use App\Models\Mercurio83;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio83.
 * Genera datos de prueba para la tabla mercurio83.
 */
class Mercurio83Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio83::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipben' => $this->faker->randomNumber(5, true),
            'tipideben' => $this->faker->randomNumber(5, true),
            'numideben' => $this->faker->text(15),
            'prinomben' => $this->faker->firstName(),
            'segnomben' => $this->faker->optional()->firstName(),
            'priapeben' => $this->faker->lastName(),
            'segapeben' => $this->faker->optional()->lastName(),
            'tipgenben' => $this->faker->optional()->randomNumber(5, true),
            'fecnacben' => $this->faker->optional()->date(),
            'codpaiben' => $this->faker->optional()->randomNumber(5, true),
            'coddep_nac' => $this->faker->optional()->randomElement(['01', '02']),
            'ciunacben' => $this->faker->numerify('#####'),
            'fecafiben' => $this->faker->optional()->date(),
            'coddep_res' => $this->faker->optional()->randomElement(['01', '02']),
            'ciuresben' => $this->faker->numerify('#####'),
            'codareresben' => $this->faker->randomNumber(5, true),
            'direccionben' => $this->faker->optional()->address(),
            'codgru' => $this->faker->randomNumber(5, true),
            'codpob' => $this->faker->randomNumber(5, true),
            'facvul' => $this->faker->randomNumber(5, true),
            'tipjor' => $this->faker->randomNumber(5, true),
            'fecina' => $this->faker->optional()->date(),
            'motivo' => $this->faker->optional()->text(200),
            'codres' => $this->faker->optional()->randomNumber(5, true),
            'codpue' => $this->faker->optional()->randomNumber(5, true),
            'responsable' => $this->faker->optional()->randomNumber(5, true),
            'nivedu' => $this->faker->optional()->randomNumber(5, true),
            'codgra' => $this->faker->optional()->randomNumber(5, true),
        ];
    }
}