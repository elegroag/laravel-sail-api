<?php

namespace Database\Factories;

use App\Models\ComandoEstructuras;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comandos>
 */
class ComandosFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $estados = ['P', 'F', 'X', 'E'];

        return [
            'fecha_runner' => fake()->optional(0.7)->date(),
            'hora_runner' => fake()->optional(0.7)->time('H:i:s'),
            'usuario' => strtoupper(fake()->bothify('??????')),
            'progreso' => fake()->numberBetween(0, 100),
            'estado' => fake()->randomElement($estados),
            'proceso' => fake()->optional(0.5)->bothify('PRO###'),
            'linea_comando' => fake()->randomElement([
                'php artisan queue:work --timeout=120',
                'php artisan schedule:run',
                'python3 script.py --env=prod',
                'node build.js --target=prod',
            ]),
            // FK a comando_estructuras
            'estructura' => ComandoEstructuras::factory(),
            'parametros' => fake()->randomElement([
                'ENV=prod;RETRIES=3',
                'ENV=dev;RETRIES=1',
                'force=true;dryRun=false',
                'plan=nightly;zone=us-east-1',
            ]),
            'resultado' => fake()->optional(0.4)->paragraph(),
        ];
    }
}
