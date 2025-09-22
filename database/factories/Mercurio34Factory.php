<?php

namespace Database\Factories;

use App\Models\Mercurio34;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio34.
 * Genera datos de prueba para la tabla mercurio34.
 */
class Mercurio34Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio34::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'nit' => $this->faker->optional()->numerify('###############'),
            'cedtra' => $this->faker->numerify('###############'),
            'cedcon' => $this->faker->optional()->numerify('###############'),
            'numdoc' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'priape' => $this->faker->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'fecnac' => $this->faker->date(),
            'ciunac' => $this->faker->numerify('#####'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'parent' => $this->faker->randomElement(['HIJO', 'HIJA', 'NIETO']),
            'huerfano' => $this->faker->randomElement(['S', 'N']),
            'tiphij' => $this->faker->optional()->randomElement(['A', 'B']),
            'nivedu' => $this->faker->optional()->randomElement(['001', '002']),
            'captra' => $this->faker->optional()->randomElement(['S', 'N']),
            'tipdis' => $this->faker->optional()->randomElement(['01', '02']),
            'calendario' => $this->faker->optional()->randomElement(['A', 'B']),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'motivo' => $this->faker->optional()->text(500),
            'fecest' => $this->faker->optional()->date(),
            'codben' => $this->faker->optional()->randomNumber(5, true),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
            'cedacu' => $this->faker->optional()->numerify('####################'),
            'fecsol' => $this->faker->optional()->date(),
            'resguardo_id' => $this->faker->optional()->numerify('####'),
            'pub_indigena_id' => $this->faker->optional()->numerify('####'),
            'peretn' => $this->faker->optional()->randomElement(['01', '02']),
            'codban' => $this->faker->optional()->numerify('####'),
            'numcue' => $this->faker->optional()->randomNumber(10, true),
            'tippag' => $this->faker->optional()->randomElement(['T', 'C']),
            'tipcue' => $this->faker->optional()->randomElement(['A', 'C']),
            'biocedu' => $this->faker->optional()->numerify('##################'),
            'biotipdoc' => $this->faker->optional()->randomElement(['CC', 'TI']),
            'bioprinom' => $this->faker->optional()->firstName(),
            'biosegnom' => $this->faker->optional()->firstName(),
            'biopriape' => $this->faker->optional()->lastName(),
            'biosegape' => $this->faker->optional()->lastName(),
            'bioemail' => $this->faker->optional()->email(),
            'biophone' => $this->faker->optional()->phoneNumber(),
            'biocodciu' => $this->faker->optional()->numerify('#####'),
            'biodire' => $this->faker->optional()->address(),
            'biourbana' => $this->faker->optional()->randomElement(['S', 'N']),
            'biodesco' => $this->faker->optional()->randomElement(['S', 'N']),
            'fecapr' => $this->faker->optional()->date(),
        ];
    }
}