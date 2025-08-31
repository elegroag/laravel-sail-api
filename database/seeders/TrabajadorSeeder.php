<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trabajador;

class TrabajadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Trabajador::factory()->create([
            'nombre' => 'Test User',
            'rut' => '12345678-9',
            'direccion' => 'Test User',
            'telefono' => '12345678',
            'email' => 'test@example.com',
            'sector_economico' => 'Test User',
            'numero_empleados' => 1,
            'descripcion' => 'Test User',
            'estado' => 'activa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
