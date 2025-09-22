<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AfiliadoHabil>
 */
class AfiliadoHabilFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Categorías comunes en el dominio (E=Empresa/T=Trabajador/C=Conyuge/B=Beneficiario/I=Independiente, etc.)
        $categorias = ['E', 'T', 'C', 'B', 'I', 'F', 'N', 'O', 'P', 'S'];

        return [
            // bigint(16) NULL
            'cedtra' => fake()->optional(0.7)->numberBetween(1_000_000, 9_999_999_999),
            'docben' => fake()->optional(0.7)->numberBetween(1_000_000, 9_999_999_999),

            // char(2) NULL
            'categoria' => fake()->optional(0.6)->randomElement($categorias),

            // varchar(120) NULL
            'nomben' => fake()->optional(0.5)->name(),

            // varchar(20) NOT NULL (código de servicio)
            'codser' => fake()->regexify('[A-Z]{2,4}[0-9]{0,4}'),

            // int(10) NOT NULL y UNIQUE
            'pin' => fake()->unique()->numberBetween(100000, 999999999),
        ];
    }
}
