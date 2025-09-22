<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mercurio01>
 */
class Mercurio01Factory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // PK codapl: exactamente 2 letras en mayúsculas (CHAR(2))
        $codapl = strtoupper($this->faker->unique()->bothify('??'));

        // Campos con longitud máxima y nullables según migración
        $email = $this->faker->optional()->safeEmail();
        if ($email !== null) {
            $email = substr($email, 0, 45);
        }

        $clave = $this->faker->optional()->bothify(str_repeat('#', 8)); // hasta 20, usamos 8-12
        if ($clave !== null) {
            $clave = substr($clave, 0, 20);
        }

        // path es NOT NULL y máx 45
        $path = substr($this->faker->lexify('?????????????????????????????????????????????'), 0, 45);

        $ftpserver = $this->faker->optional()->hostname();
        if ($ftpserver !== null) {
            $ftpserver = substr($ftpserver, 0, 45);
        }

        $pathserver = $this->faker->optional()->lexify('?????????????????????????????????????????????');
        if ($pathserver !== null) {
            $pathserver = substr($pathserver, 0, 45);
        }

        $userserver = $this->faker->optional()->userName();
        if ($userserver !== null) {
            $userserver = substr($userserver, 0, 45);
        }

        $passserver = $this->faker->optional()->password(8, 20);
        if ($passserver !== null) {
            $passserver = substr($passserver, 0, 45);
        }

        return [
            'codapl' => $codapl,
            'email' => $email,
            'clave' => $clave,
            'path' => $path,
            'ftpserver' => $ftpserver,
            'pathserver' => $pathserver,
            'userserver' => $userserver,
            'passserver' => $passserver,
        ];
    }
}
