<?php

namespace Database\Factories;

use App\Models\Mercurio30;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio30.
 * Genera datos de prueba para la tabla mercurio30.
 */
class Mercurio30Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio30::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'nit' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'razsoc' => $this->faker->company(),
            'priape' => $this->faker->optional()->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->optional()->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'sigla' => $this->faker->optional()->text(45),
            'digver' => $this->faker->optional()->randomElement(['0', '1']),
            'tipper' => $this->faker->optional()->randomElement(['N', 'J']),
            'calemp' => $this->faker->randomElement(['A', 'B', 'C']),
            'cedrep' => $this->faker->numerify('#############'),
            'repleg' => $this->faker->text(140),
            'direccion' => $this->faker->address(),
            'codciu' => $this->faker->numerify('#####'),
            'codzon' => $this->faker->optional()->numerify('#########'),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'fax' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->email(),
            'codact' => $this->faker->numerify('######'),
            'fecini' => $this->faker->optional()->date(),
            'tottra' => $this->faker->numberBetween(1, 100),
            'valnom' => $this->faker->numberBetween(1000000, 10000000),
            'tipsoc' => $this->faker->randomElement(['EMP', 'IND']),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'motivo' => $this->faker->optional()->text(800),
            'fecest' => $this->faker->optional()->date(),
            'usuario' => $this->faker->randomNumber(5, true),
            'dirpri' => $this->faker->optional()->address(),
            'ciupri' => $this->faker->optional()->numerify('#####'),
            'telpri' => $this->faker->optional()->phoneNumber(),
            'celpri' => $this->faker->optional()->phoneNumber(),
            'emailpri' => $this->faker->optional()->email(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
            'tipemp' => $this->faker->optional()->text(50),
            'matmer' => $this->faker->optional()->numerify('############'),
            'coddocrepleg' => $this->faker->optional()->randomElement(['001', '002']),
            'priaperepleg' => $this->faker->optional()->lastName(),
            'segaperepleg' => $this->faker->optional()->lastName(),
            'prinomrepleg' => $this->faker->optional()->firstName(),
            'segnomrepleg' => $this->faker->optional()->firstName(),
            'codcaj' => $this->faker->optional()->numerify('##'),
            'fecsol' => $this->faker->optional()->date(),
            'fecapr' => $this->faker->optional()->date(),
        ];
    }
}
