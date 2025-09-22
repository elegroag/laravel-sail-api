<?php

namespace Database\Factories;

use App\Models\Mercurio38;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio38.
 * Genera datos de prueba para la tabla mercurio38.
 */
class Mercurio38Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio38::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('##################'),
            'calemp' => $this->faker->randomElement(['A', 'B', 'C']),
            'log' => $this->faker->optional()->randomNumber(5, true),
            'cedtra' => $this->faker->randomNumber(10, true),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'priape' => $this->faker->optional()->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->optional()->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'fecnac' => $this->faker->optional()->date(),
            'ciunac' => $this->faker->optional()->numerify('######'),
            'sexo' => $this->faker->optional()->randomElement(['M', 'F']),
            'estciv' => $this->faker->optional()->randomElement(['SO', 'CA', 'DI']),
            'cabhog' => $this->faker->optional()->randomElement(['S', 'N']),
            'codciu' => $this->faker->optional()->numerify('######'),
            'codzon' => $this->faker->optional()->numerify('######'),
            'direccion' => $this->faker->optional()->address(),
            'barrio' => $this->faker->optional()->text(45),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->email(),
            'fecini' => $this->faker->optional()->date(),
            'salario' => $this->faker->optional()->numerify('##########'),
            'captra' => $this->faker->optional()->randomElement(['S', 'N']),
            'tipdis' => $this->faker->optional()->randomElement(['01', '02']),
            'nivedu' => $this->faker->optional()->randomElement(['01', '02']),
            'rural' => $this->faker->optional()->randomElement(['S', 'N']),
            'vivienda' => $this->faker->optional()->randomElement(['P', 'A']),
            'tipafi' => $this->faker->optional()->randomElement(['AF', 'CO']),
            'autoriza' => $this->faker->optional()->randomElement(['S', 'N']),
            'motivo' => $this->faker->optional()->text(300),
            'codact' => $this->faker->numerify('######'),
            'fecsol' => $this->faker->optional()->date(),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'fecest' => $this->faker->optional()->date(),
            'usuario' => $this->faker->optional()->randomNumber(5, true),
            'coddocrepleg' => $this->faker->randomElement(['001', '002']),
            'peretn' => $this->faker->randomElement(['01', '02']),
            'resguardo_id' => $this->faker->numerify('####'),
            'pub_indigena_id' => $this->faker->numerify('####'),
            'facvul' => $this->faker->randomElement(['01', '02']),
            'orisex' => $this->faker->randomElement(['001', '002']),
            'tippag' => $this->faker->randomElement(['T', 'C']),
            'numcue' => $this->faker->numerify('####################'),
            'codcaj' => $this->faker->numerify('###'),
            'cargo' => $this->faker->randomElement(['001', '002']),
            'codban' => $this->faker->optional()->numerify('###'),
            'tipcue' => $this->faker->optional()->randomElement(['A', 'C']),
            'fecapr' => $this->faker->optional()->date(),
        ];
    }
}