<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio52;

class Mercurio52Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $menus = [
            ['codmen' => 1, 'detalle' => 'APORTES', 'codare' => 1, 'url' => 'Aportes', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 2, 'detalle' => 'NUCLEO', 'codare' => 1, 'url' => 'Nucleo', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 3, 'detalle' => 'GIRO', 'codare' => 1, 'url' => 'Giro', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 4, 'detalle' => 'NO GIRO', 'codare' => 1, 'url' => 'NoGiro', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 5, 'detalle' => 'FORMULARIO', 'codare' => 2, 'url' => 'Formularios', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 6, 'detalle' => 'CREDITOS', 'codare' => 3, 'url' => 'Creditos', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 7, 'detalle' => 'SIMULADOR', 'codare' => 3, 'url' => 'Simulador', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 8, 'detalle' => 'TARJETAS', 'codare' => 4, 'url' => 'Tarjetas', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 9, 'detalle' => 'PQRS', 'codare' => 5, 'url' => 'Quejas', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 10, 'detalle' => 'SERVICIOS', 'codare' => 6, 'url' => 'Servicios', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 11, 'detalle' => 'COMPRAS', 'codare' => 6, 'url' => 'Compras', 'tipo' => 'T', 'estado' => 'I'],
            ['codmen' => 12, 'detalle' => 'CALIFICACIONES', 'codare' => 7, 'url' => 'Calificaciones', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 14, 'detalle' => 'MATRICULAS', 'codare' => 8, 'url' => 'Matriculas', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 15, 'detalle' => 'SEDES', 'codare' => 9, 'url' => 'Sedes', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 16, 'detalle' => 'ENTRADAS', 'codare' => 9, 'url' => 'Entradas', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 17, 'detalle' => 'PAGOS', 'codare' => 9, 'url' => 'Pagos', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 18, 'detalle' => 'ENTRAR', 'codare' => 9, 'url' => 'Entrar', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 19, 'detalle' => 'MAPA', 'codare' => 6, 'url' => 'Mapa', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 20, 'detalle' => 'RADICADOS', 'codare' => 10, 'url' => 'Radicados', 'tipo' => 'T', 'estado' => 'A'],
            ['codmen' => 21, 'detalle' => 'SERVICIOS', 'codare' => 11, 'url' => 'Servicios', 'tipo' => 'B', 'estado' => 'A'],
        ];

        foreach ($menus as $menu) {
            Mercurio52::updateOrCreate(
                ['codmen' => $menu['codmen']],
                $menu
            );
        }
    }
}
