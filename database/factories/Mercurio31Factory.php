<?php

namespace Database\Factories;

use App\Models\Mercurio31;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio31.
 * Genera datos de prueba para la tabla mercurio31.
 */
class Mercurio31Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio31::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'nit' => $this->faker->numerify('###############'),
            'razsoc' => $this->faker->company(),
            'cedtra' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'priape' => $this->faker->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'fecnac' => $this->faker->date(),
            'ciunac' => $this->faker->numerify('#####'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'orisex' => $this->faker->optional()->randomElement(['M', 'F']),
            'estciv' => $this->faker->randomElement(['SO', 'CA', 'DI']),
            'cabhog' => $this->faker->randomElement(['S', 'N']),
            'codciu' => $this->faker->optional()->numerify('#####'),
            'codzon' => $this->faker->optional()->numerify('#########'),
            'direccion' => $this->faker->optional()->address(),
            'barrio' => $this->faker->optional()->text(48),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'fax' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->email(),
            'fecsol' => $this->faker->date(),
            'fecing' => $this->faker->date(),
            'salario' => $this->faker->numberBetween(1000000, 10000000),
            'tipsal' => $this->faker->optional()->randomElement(['M', 'Q']),
            'captra' => $this->faker->optional()->randomElement(['S', 'N']),
            'tipdis' => $this->faker->optional()->randomElement(['01', '02']),
            'nivedu' => $this->faker->optional()->randomElement(['001', '002']),
            'rural' => $this->faker->randomElement(['S', 'N']),
            'horas' => $this->faker->numberBetween(8, 48),
            'tipcon' => $this->faker->randomElement(['F', 'T']),
            'trasin' => $this->faker->optional()->randomElement(['S', 'N']),
            'vivienda' => $this->faker->randomElement(['PROP', 'ARRE']),
            'tipafi' => $this->faker->randomElement(['AF', 'CO']),
            'profesion' => $this->faker->optional()->text(45),
            'cargo' => $this->faker->optional()->randomElement(['001', '002']),
            'autoriza' => $this->faker->optional()->randomElement(['S', 'N']),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'motivo' => $this->faker->optional()->text(500),
            'fecest' => $this->faker->optional()->date(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
            'facvul' => $this->faker->optional()->randomElement(['01', '02']),
            'peretn' => $this->faker->optional()->randomElement(['01', '02']),
            'dirlab' => $this->faker->optional()->address(),
            'ruralt' => $this->faker->optional()->randomElement(['S', 'N']),
            'comision' => $this->faker->optional()->randomElement(['S', 'N']),
            'tipjor' => $this->faker->optional()->randomElement(['M', 'T']),
            'codsuc' => '001',
            'fecha_giro' => $this->faker->optional()->date('Y-m-d'),
            'tippag' => 'T',
            'numcue' => 0,
            'otra_empresa' => $this->faker->optional()->company(),
            'resguardo_id' => $this->faker->optional()->numerify('#####'),
            'pub_indigena_id' => $this->faker->optional()->numerify('###'),
            'codban' => $this->faker->optional()->numerify('####'),
            'tipcue' => $this->faker->optional()->randomElement(['A', 'C']),
            'fecafi' => $this->faker->optional()->date(),
            'fecapr' => $this->faker->optional()->date(),
        ];
    }
}
