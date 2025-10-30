<?php

namespace Database\Seeders;

use App\Models\ApiEndpoint;
use Illuminate\Database\Seeder;

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
                'service_name' => 'CuotaMonetaria',
                'endpoint_name' => 'cuotas_by_empresa_and_periodo',
                'connection_name' => 'api-clisisu',
                'host_dev' => 'http://172.168.0.40:9000',
                'host_pro' => 'http://172.168.0.7:9000',
            ],
            [
                'service_name' => 'PoblacionAfiliada',
                'endpoint_name' => 'poblation',
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
            [
                'service_name' => 'Python',
                'endpoint_name' => 'generate-pdf',
                'connection_name' => 'api-python',
                'host_dev' => 'http://localhost:5000/api',
                'host_pro' => 'http://localhost:5000/api',
            ],
        ];

        foreach ($endpoints as $endpoint) {
            ApiEndpoint::create(
                [
                    'service_name' => $endpoint['service_name'],
                    'endpoint_name' => $endpoint['endpoint_name'],
                    'connection_name' => $endpoint['connection_name'],
                    'host_dev' => $endpoint['host_dev'],
                    'host_pro' => $endpoint['host_pro'],
                ]
            );
        }
    }
}
