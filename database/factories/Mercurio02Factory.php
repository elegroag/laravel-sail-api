<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio02>
 */
class Mercurio02Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // PK codcaj: exactamente 6 caracteres mayúsculas/números (CHAR(6))
        $codcaj = strtoupper($this->faker->unique()->bothify('??###?'));
        $codcaj = substr($codcaj, 0, 6);

        // Campos con tamaños definidos en la migración
        $nit = substr($this->faker->numerify(str_repeat('#', 10)) . $this->faker->randomDigit(), 0, 13);
        $razsoc = substr($this->faker->company(), 0, 60);
        $sigla = substr(strtoupper($this->faker->lexify('??????')), 0, 30);
        $email = substr($this->faker->companyEmail(), 0, 90);
        $direccion = substr($this->faker->address(), 0, 90);
        $telefono = substr($this->faker->numerify('##########'), 0, 20);
        $codciu = substr($this->faker->numerify('#####'), 0, 5);
        $pagweb = substr($this->faker->url(), 0, 45);
        $pagfac = $this->faker->optional()->url();
        $pagtwi = $this->faker->optional()->url();
        $pagyou = $this->faker->optional()->url();

        $pagfac = $pagfac ? substr($pagfac, 0, 45) : null;
        $pagtwi = $pagtwi ? substr($pagtwi, 0, 45) : null;
        $pagyou = $pagyou ? substr($pagyou, 0, 45) : null;

        return [
            'codcaj' => $codcaj,
            'nit' => $nit,
            'razsoc' => $razsoc,
            'sigla' => $sigla,
            'email' => $email,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'codciu' => $codciu,
            'pagweb' => $pagweb,
            'pagfac' => $pagfac,
            'pagtwi' => $pagtwi,
            'pagyou' => $pagyou,
        ];
    }
}
