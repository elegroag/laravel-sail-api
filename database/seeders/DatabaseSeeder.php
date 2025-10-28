<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            // Configuraciones y menús
            ApiEndpointsSeeder::class,
            ComandoEstructuraSeeder::class,
            ComandoSeeder::class,
            MenuItemSeeder::class,
            MenuTipoSeeder::class,
            NotificacionesSeeder::class,
            RecepcionSatSeeder::class,
            ServiciosCuposSeeder::class,
            Subsi54Seeder::class,
            TranomsSeeder::class,

            // Tablas Gener
            Gener21Seeder::class,
            Gener02Seeder::class,
            Gener09Seeder::class,
            Gener18Seeder::class,
            Gener40Seeder::class,
            Gener42Seeder::class,

            MenuPermissionSeeder::class,

            // Tablas Mercurio
            Mercurio01Seeder::class,
            Mercurio02Seeder::class,
            Mercurio03Seeder::class,
            Mercurio04Seeder::class,
            Mercurio05Seeder::class,
            Mercurio06Seeder::class,
            Mercurio07Seeder::class,
            Mercurio09Seeder::class,
            
            Mercurio08Seeder::class,
            Mercurio11Seeder::class,
            Mercurio10Seeder::class,

            Mercurio12Seeder::class,
            Mercurio13Seeder::class,
            Mercurio14Seeder::class,
            Mercurio15Seeder::class,
            Mercurio16Seeder::class,
            Mercurio18Seeder::class,
            Mercurio19Seeder::class,
            Mercurio20Seeder::class,
            Mercurio26Seeder::class,
            Mercurio28Seeder::class,
            Mercurio30Seeder::class,
            Mercurio31Seeder::class,
            Mercurio32Seeder::class,
            Mercurio33Seeder::class,
            Mercurio34Seeder::class,
            Mercurio35Seeder::class,
            Mercurio36Seeder::class,
            Mercurio37Seeder::class,
            Mercurio38Seeder::class,
            Mercurio39Seeder::class,
            Mercurio40Seeder::class,
            Mercurio41Seeder::class,
            Mercurio45Seeder::class,
            Mercurio46Seeder::class,
            Mercurio47Seeder::class,

            Mercurio50Seeder::class,
            Mercurio51Seeder::class,
            Mercurio52Seeder::class,
            Mercurio53Seeder::class,
            Mercurio54Seeder::class,
            
            Mercurio55Seeder::class,
            Mercurio56Seeder::class,
            Mercurio57Seeder::class,
            Mercurio58Seeder::class,
            Mercurio59Seeder::class,
            Mercurio60Seeder::class,
            Mercurio61Seeder::class,
            Mercurio62Seeder::class,
            Mercurio63Seeder::class,

            Mercurio64Seeder::class,
            
            Mercurio67Seeder::class,
            Mercurio65Seeder::class,
            Mercurio66Seeder::class,
            
            Mercurio68Seeder::class,
            Mercurio69Seeder::class,
            Mercurio71Seeder::class,
            Mercurio72Seeder::class,
            Mercurio73Seeder::class,
            Mercurio74Seeder::class,
            // Tablas XML
            Xml4b004Seeder::class,
            Xml4b005Seeder::class,
            Xml4b064Seeder::class,
            Xml4b070Seeder::class,
            Xml4b081Seeder::class,
            Xml4b086Seeder::class,
            Xml4b087Seeder::class,
            Xml4b091Seeder::class,
            Xml4b094Seeder::class,

            Mercusat02Seeder::class,
        ];

        try {
            DB::transaction(function () use ($seeders): void {
                foreach ($seeders as $seederClass) {
                    // Ejecutar seeder registrado dentro de la transacción para garantizar rollback
                    $this->call($seederClass);
                }
            });
        } catch (\Throwable $exception) {
            // Registrar el error y propagarlo para detener el proceso de seeding
            Log::error('Error al ejecutar los seeders principales', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
