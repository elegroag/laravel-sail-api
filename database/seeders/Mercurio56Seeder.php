<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio56;

class Mercurio56Seeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        $servicios = [
            ['codinf' => 'CCF013-01-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ALQUILER AUDITORIOS', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO THE ROCK GYM FITNESS', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00003', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO BODY STUDIO', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00004', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO GYM CLUB', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00005', 'archivo' => null, 'email' => 'publicidad@comfaca.com', 'telefono' => '3144845126', 'nota' => 'GIMNASIO PARADISE GYM CROSS', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00007', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO GOLDS GYM', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00008', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ACADEMIA DE TAEKWONDO HANKOOK', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00009', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CLUB DE PORRISMO FCA CAQUETA', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00013', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO VIP FITNESS CENTER', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00015', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ESCAPE ROMANTICO  BELLO HORIZONTE', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00016', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'VOLEIBOL  CLUB DEPORTIVO FIORENZA', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00017', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CAMPEONATO EL PAUJIL - BANQUITAS', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00018', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CAMPEONATO INTENSIVO DECEMBRINO', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00020', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'SPINNING VIAJERO', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00022', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'CAMPEONATO PUERTO RICO', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00024', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CAMPEONATO EL DONCELLO - VOLEIBOL', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00027', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO RU2', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00032', 'archivo' => null, 'email' => 'recreacion@comfaca.com', 'telefono' => null, 'nota' => 'CAMPEONATO EL PAUJIL', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00033', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'BINGO FAMILIAR LA MONTAÑITA', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00034', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'BINGO FAMILIAR MILAN', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00038', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'BINGO FAMILIAR SOLANO', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00042', 'archivo' => null, 'email' => 'chaloradio@gmail.com', 'telefono' => '0', 'nota' => 'CENTRO RECREATIVO BURITI', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00043', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'GIMNASIO BODY CENTER', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00044', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ESCUELA DE FUTBOL  CLUB DEPORTIVO FLORENCIA', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00046', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO TEMPLO FIT', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00047', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO MUSCLEFIT SPORT CENTER', 'estado' => 'A'],
            ['codinf' => 'CCF013-01-00049', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ESCUELA DE NATACION ACADEMIA FORTALEZA', 'estado' => 'A'],
            ['codinf' => 'CCF013-06-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ESPECTACULOS ARTES ESCENICAS 2025', 'estado' => 'A'],
            ['codinf' => 'CCF013-06-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'SALA DE PROYECCIONES', 'estado' => 'A'],
            ['codinf' => 'CCF013-06-00005', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'SHOW DE PIROBERTA', 'estado' => 'A'],
            ['codinf' => 'CCF013-06-00009', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'DIA DE LA MADRE', 'estado' => 'A'],
            ['codinf' => 'CCF013-07-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'SALA FITNESS', 'estado' => 'A'],
            ['codinf' => 'CCF013-07-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'VELADA ROMANTICA', 'estado' => 'A'],
            ['codinf' => 'CCF013-07-00003', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'BINGO FLORENCIA', 'estado' => 'A'],
            ['codinf' => 'CCF013-07-00006', 'archivo' => null, 'email' => 'tesorero@comfaca.com', 'telefono' => '0', 'nota' => 'COMPLEMENTO NUTRICIONAL', 'estado' => 'A'],
            ['codinf' => 'CCF013-07-00008', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CLUB DE TAREAS UIS 2025', 'estado' => 'A'],
            ['codinf' => 'CCF013-12-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'ACADEMIA PORRAS', 'estado' => 'A'],
            ['codinf' => 'CCF013-14-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'NOCHE DE REYES Y CABALLOS', 'estado' => 'A'],
            ['codinf' => 'CCF013-15-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'CURSO VACACIONAL 2025', 'estado' => 'A'],
            ['codinf' => 'CCF013-19-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'DIA DEL PADRE', 'estado' => 'A'],
            ['codinf' => 'CCF013-26-00001', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'DISCAPACIDAD  CLUB LA ESPERANZA', 'estado' => 'A'],
            ['codinf' => 'CCF013-26-00002', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'ADULTO MAYOR  CLUB POCA DORADA', 'estado' => 'A'],
            ['codinf' => 'CCF013-30-00007', 'archivo' => null, 'email' => 'chaloradio@gmail.com', 'telefono' => '0', 'nota' => 'GIMNASIO IMPERIO PRO', 'estado' => 'A'],
            ['codinf' => 'CCF013-30-00042', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => '0', 'nota' => 'BINGO EL DONCELLO', 'estado' => 'A'],
            ['codinf' => 'CCF013-30-00100', 'archivo' => null, 'email' => 'atencionalusuario@comfaca.com', 'telefono' => null, 'nota' => 'GIMNASIO BODYTECH', 'estado' => 'A'],
        ];

        foreach ($servicios as $servicio) {
            Mercurio56::updateOrCreate(
                ['codinf' => $servicio['codinf']],
                $servicio
            );
        }
    }
}
