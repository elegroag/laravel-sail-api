<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiEndpoint;

class ApiEndpointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $endpoints = [
            [
                'service_name' => 'ComfacaEmpresas',
                'endpoint_name' => 'company',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',

            ],
            [
                'service_name' => 'ComfacaAfilia',
                'endpoint_name' => 'affiliation',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'AportesEmpresas',
                'endpoint_name' => 'aportes',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Correspondencias',
                'endpoint_name' => 'correspondencia',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Novedades',
                'endpoint_name' => 'novedades',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'ServicioSat',
                'endpoint_name' => 'sat',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Tesoreria',
                'endpoint_name' => 'tesoreria',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'CruzarDaviplata',
                'endpoint_name' => 'tippag',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Reprocesos',
                'endpoint_name' => 'sat',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Certificados',
                'endpoint_name' => 'certificados',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Usuarios',
                'endpoint_name' => 'usuarios',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Funcionalidades',
                'endpoint_name' => 'satservice',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'Whatsapp',
                'endpoint_name' => 'api',
                'connection_name' => 'api-whatsapp',
                'host_dev' => 'http://172.168.0.7:3001',
                'host_pro' => 'http://172.168.0.7:3001',
            ],
        ];

        foreach ($endpoints as $endpoint) {
            ApiEndpoint::updateOrCreate(
                [
                    'service_name' => $endpoint['service_name'],
                    'endpoint_name' => $endpoint['endpoint_name'],
                ],
                [
                    'connection_name' => $endpoint['connection_name'],
                ]
            );
        }
    }
}
