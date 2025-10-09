<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiEndpoint>
 */
class ApiEndpointFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = fake()->randomElement([
            'sat', 'cajas', 'mercurio', 'aportesoracle', 'firmas', 'reportes',
        ]);

        // Generamos un endpoint legible y único para evitar choques
        $endpointBase = fake()->unique()->randomElement([
            'consulta', 'guardar', 'actualizar', 'eliminar', 'descargar', 'sincronizar',
        ]);
        $endpoint = $endpointBase.'-'.fake()->unique()->numberBetween(1, 99999);

        return [
            'service_name' => $service,
            'endpoint_name' => $endpoint,
            'connection_name' => fake()->optional(0.5)->randomElement([
                'mysql', 'pgsql', 'sqlsrv', 'oracle', 'mongo',
            ]),
        ];
    }
}
