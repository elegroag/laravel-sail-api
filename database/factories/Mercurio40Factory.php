<?php

namespace Database\Factories;

use App\Models\Mercurio40;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Mercurio40.
 * Genera datos de prueba para la tabla mercurio40.
 */
class Mercurio40Factory extends Factory
{
    /**
     * El modelo asociado con este factory.
     */
    protected $model = Mercurio40::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'log' => $this->faker->randomNumber(5, true),
            'cedtra' => $this->faker->numerify('###############'),
            'tipdoc' => $this->faker->randomElement(['CC', 'TI', 'CE']),
            'priape' => $this->faker->lastName(),
            'segape' => $this->faker->optional()->lastName(),
            'prinom' => $this->faker->firstName(),
            'segnom' => $this->faker->optional()->firstName(),
            'fecnac' => $this->faker->date(),
            'ciunac' => $this->faker->numerify('#####'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'estciv' => $this->faker->randomElement(['SO', 'CA', 'DI']),
            'cabhog' => $this->faker->randomElement(['S', 'N']),
            'codciu' => $this->faker->numerify('#####'),
            'codzon' => $this->faker->numerify('#########'),
            'direccion' => $this->faker->address(),
            'barrio' => $this->faker->optional()->text(8),
            'telefono' => $this->faker->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'fax' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->email(),
            'fecing' => $this->faker->date(),
            'salario' => $this->faker->numberBetween(1000000, 10000000),
            'captra' => $this->faker->optional()->randomElement(['S', 'N']),
            'tipdis' => $this->faker->optional()->randomElement(['01', '02']),
            'nivedu' => $this->faker->optional()->randomElement(['001', '002']),
            'rural' => $this->faker->randomElement(['S', 'N']),
            'vivienda' => $this->faker->randomElement(['PROP', 'ARRE']),
            'tipafi' => $this->faker->randomElement(['AF', 'CO']),
            'autoriza' => $this->faker->optional()->randomElement(['S', 'N']),
            'codact' => $this->faker->numerify('######'),
            'calemp' => $this->faker->randomElement(['A', 'B', 'C']),
            'usuario' => $this->faker->randomNumber(5, true),
            'estado' => $this->faker->randomElement(['A', 'I']),
            'codest' => $this->faker->optional()->numerify('##'),
            'motivo' => $this->faker->optional()->text(100),
            'fecest' => $this->faker->optional()->date(),
            'tipo' => $this->faker->randomElement(['01', '02']),
            'coddoc' => $this->faker->randomElement(['01', '02']),
            'documento' => $this->faker->numerify('###############'),
        ];
    }
}
