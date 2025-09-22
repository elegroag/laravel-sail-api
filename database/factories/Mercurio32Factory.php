<?php

namespace Database\Factories;

use App\Models\Mercurio32;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio32.
 * Genera datos de prueba para la tabla mercurio32.
 */
class Mercurio32Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio32::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'cedtra' => $this->faker->numerify('###############'),
            'cedcon' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'priape' => $this->faker->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'fecnac' => $this->faker->date(),
            'ciunac' => $this->faker->numerify('#####'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'estciv' => $this->faker->randomElement(['SO', 'CA', 'DI']),
            'comper' => $this->faker->randomElement(['S', 'N']),
            'tiecon' => $this->faker->numberBetween(0, 10),
            'ciures' => $this->faker->optional()->numerify('#####'),
            'codzon' => $this->faker->optional()->numerify('#########'),
            'tipviv' => $this->faker->optional()->randomElement(['01', '02']),
            'direccion' => $this->faker->address(),
            'barrio' => $this->faker->optional()->text(45),
            'telefono' => $this->faker->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->email(),
            'nivedu' => $this->faker->optional()->randomElement(['001', '002']),
            'fecing' => $this->faker->date(),
            'codocu' => $this->faker->optional()->numerify('#####'),
            'salario' => $this->faker->numberBetween(1000000, 10000000),
            'tipsal' => $this->faker->optional()->randomElement(['M', 'Q']),
            'captra' => $this->faker->optional()->randomElement(['S', 'N']),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'motivo' => $this->faker->optional()->text(500),
            'fecest' => $this->faker->optional()->date(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
            'fecsol' => $this->faker->optional()->date(),
            'tippag' => 'T',
            'numcue' => 0,
            'empresalab' => $this->faker->optional()->company(),
            'codban' => $this->faker->optional()->numerify('####'),
            'resguardo_id' => $this->faker->optional()->numerify('#####'),
            'pub_indigena_id' => $this->faker->optional()->numerify('###'),
            'tipcue' => $this->faker->optional()->randomElement(['A', 'C']),
            'tipdis' => $this->faker->optional()->randomElement(['01', '02']),
            'peretn' => '7',
            'fecapr' => $this->faker->optional()->date(),
        ];
    }
}