<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio07>
 */
class Mercurio07Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // tipo: coincide con Mercurio06 (1 letra válida), almacenada en CHAR(2)
        $tipo = $this->faker->randomElement(['P','T','E','I','O','F','S']);

        // coddoc: 2 letras (tipo de documento)
        $coddoc = strtoupper($this->faker->lexify('??'));

        // documento: hasta 15 chars, usamos numérico
        $documento = substr($this->faker->numerify(str_repeat('#', 10) . '#####'), 0, 15);

        $nombre = substr($this->faker->name(), 0, 120);
        $email = substr($this->faker->safeEmail(), 0, 60);

        // clave: hash tipo bcrypt (60 chars), cabe en CHAR(255)
        $clave = password_hash('Passw0rd!' . $documento, PASSWORD_BCRYPT);

        $feccla = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
        $autoriza = $this->faker->randomElement(['S','N']);
        $codciu = str_pad((string) $this->faker->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
        $fecreg = $this->faker->dateTimeBetween($feccla, 'now')->format('Y-m-d');
        $estado = $this->faker->randomElement(['A','I','P','X']);
        $fechaSyn = $this->faker->optional()->date('Y-m-d');
        $whatsapp = $this->faker->optional()->numerify('##########');

        return [
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
            'nombre' => $nombre,
            'email' => $email,
            'clave' => $clave,
            'feccla' => $feccla,
            'autoriza' => $autoriza,
            'codciu' => $codciu,
            'fecreg' => $fecreg,
            'estado' => $estado,
            'fecha_syncron' => $fechaSyn,
            'whatsapp' => $whatsapp,
        ];
    }

    /**
     * Estado para usuario activo.
     */
    public function activo(): self
    {
        return $this->state(fn () => ['estado' => 'A']);
    }
}
