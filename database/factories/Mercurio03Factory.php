<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio03>
 */
class Mercurio03Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // PK codfir: exactamente 3 letras mayúsculas (CHAR(3))
        $codfir = strtoupper($this->faker->unique()->bothify('???'));

        $nombre = substr($this->faker->name(), 0, 60);
        $cargo = substr($this->faker->jobTitle(), 0, 60);

        $archivo = $this->faker->optional()->lexify('??????????????????????????????'); // máx 30
        if ($archivo !== null) {
            $archivo = substr($archivo, 0, 30);
        }

        $email = $this->faker->optional()->safeEmail();
        if ($email !== null) {
            $email = substr($email, 0, 60);
        }

        return [
            'codfir' => $codfir,
            'nombre' => $nombre,
            'cargo' => $cargo,
            'archivo' => $archivo,
            'email' => $email,
        ];
    }
}
