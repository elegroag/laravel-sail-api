<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\NucleoFamiliar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '89123456'
        ]);

        // Crear empresas de prueba
        $empresa1 = Empresa::create([
            'nombre' => 'TechCorp S.A.',
            'rut' => '76.123.456-7',
            'direccion' => 'Av. Providencia 1234, Santiago',
            'telefono' => '+56 2 2345 6789',
            'email' => 'contacto@techcorp.cl',
            'sector_economico' => 'Tecnología',
            'numero_empleados' => 50,
            'descripcion' => 'Empresa líder en desarrollo de software y soluciones tecnológicas',
            'estado' => 'activa'
        ]);

        $empresa2 = Empresa::create([
            'nombre' => 'Constructora del Sur Ltda.',
            'rut' => '78.987.654-3',
            'direccion' => 'Calle Los Robles 567, Temuco',
            'telefono' => '+56 45 234 5678',
            'email' => 'info@constructoradelsur.cl',
            'sector_economico' => 'Construcción',
            'numero_empleados' => 120,
            'descripcion' => 'Especialistas en construcción residencial y comercial',
            'estado' => 'activa'
        ]);

        // Crear trabajadores de prueba
        $trabajador1 = Trabajador::create([
            'nombres' => 'Juan Carlos',
            'apellidos' => 'González Pérez',
            'rut' => '12.345.678-9',
            'email' => 'juan.gonzalez@techcorp.cl',
            'telefono' => '+56 9 8765 4321',
            'fecha_nacimiento' => '1985-03-15',
            'genero' => 'masculino',
            'direccion' => 'Las Condes 890, Santiago',
            'cargo' => 'Desarrollador Senior',
            'salario' => 2500000,
            'fecha_ingreso' => '2020-01-15',
            'estado' => 'activo',
            'empresa_id' => $empresa1->id
        ]);

        $trabajador2 = Trabajador::create([
            'nombres' => 'María Elena',
            'apellidos' => 'Rodríguez Silva',
            'rut' => '15.678.901-2',
            'email' => 'maria.rodriguez@techcorp.cl',
            'telefono' => '+56 9 7654 3210',
            'fecha_nacimiento' => '1990-07-22',
            'genero' => 'femenino',
            'direccion' => 'Ñuñoa 456, Santiago',
            'cargo' => 'Diseñadora UX/UI',
            'salario' => 2200000,
            'fecha_ingreso' => '2021-03-10',
            'estado' => 'activo',
            'empresa_id' => $empresa1->id
        ]);

        $trabajador3 = Trabajador::create([
            'nombres' => 'Pedro Antonio',
            'apellidos' => 'Martínez López',
            'rut' => '18.234.567-8',
            'email' => 'pedro.martinez@constructoradelsur.cl',
            'telefono' => '+56 9 5432 1098',
            'fecha_nacimiento' => '1978-11-05',
            'genero' => 'masculino',
            'direccion' => 'Temuco Centro 123, Temuco',
            'cargo' => 'Jefe de Obra',
            'salario' => 3000000,
            'fecha_ingreso' => '2018-06-01',
            'estado' => 'activo',
            'empresa_id' => $empresa2->id
        ]);

        // Crear núcleos familiares de prueba
        NucleoFamiliar::create([
            'nombres' => 'Ana María',
            'apellidos' => 'Pérez Soto',
            'rut' => '14.567.890-1',
            'fecha_nacimiento' => '1987-08-12',
            'genero' => 'femenino',
            'parentesco' => 'conyuge',
            'telefono' => '+56 9 8765 4322',
            'email' => 'ana.perez@gmail.com',
            'direccion' => 'Las Condes 890, Santiago',
            'estado_civil' => 'casado',
            'ocupacion' => 'Profesora',
            'dependiente_economico' => false,
            'trabajador_id' => $trabajador1->id
        ]);

        NucleoFamiliar::create([
            'nombres' => 'Sofía',
            'apellidos' => 'González Pérez',
            'rut' => '25.123.456-7',
            'fecha_nacimiento' => '2015-04-20',
            'genero' => 'femenino',
            'parentesco' => 'hija',
            'direccion' => 'Las Condes 890, Santiago',
            'estado_civil' => 'soltero',
            'dependiente_economico' => true,
            'trabajador_id' => $trabajador1->id
        ]);

        NucleoFamiliar::create([
            'nombres' => 'Carlos Eduardo',
            'apellidos' => 'Rodríguez Silva',
            'rut' => '16.789.012-3',
            'fecha_nacimiento' => '1988-12-03',
            'genero' => 'masculino',
            'parentesco' => 'conyuge',
            'telefono' => '+56 9 7654 3211',
            'email' => 'carlos.rodriguez@gmail.com',
            'direccion' => 'Ñuñoa 456, Santiago',
            'estado_civil' => 'casado',
            'ocupacion' => 'Ingeniero Civil',
            'dependiente_economico' => false,
            'trabajador_id' => $trabajador2->id
        ]);

        NucleoFamiliar::create([
            'nombres' => 'Isabel',
            'apellidos' => 'Martínez Contreras',
            'rut' => '19.345.678-9',
            'fecha_nacimiento' => '1980-02-14',
            'genero' => 'femenino',
            'parentesco' => 'conyuge',
            'telefono' => '+56 9 5432 1099',
            'email' => 'isabel.martinez@gmail.com',
            'direccion' => 'Temuco Centro 123, Temuco',
            'estado_civil' => 'casado',
            'ocupacion' => 'Enfermera',
            'dependiente_economico' => false,
            'trabajador_id' => $trabajador3->id
        ]);

        NucleoFamiliar::create([
            'nombres' => 'Diego',
            'apellidos' => 'Martínez Contreras',
            'rut' => '22.456.789-0',
            'fecha_nacimiento' => '2010-09-18',
            'genero' => 'masculino',
            'parentesco' => 'hijo',
            'direccion' => 'Temuco Centro 123, Temuco',
            'estado_civil' => 'soltero',
            'dependiente_economico' => true,
            'trabajador_id' => $trabajador3->id
        ]);
    }
}
