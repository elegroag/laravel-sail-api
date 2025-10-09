<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Mercurio28Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $camposFormulario = [
            // Campos para EMPRESA (E)
            ['tipo' => 'E', 'campo' => 'barrio_comercial', 'detalle' => 'BARRIO COMERCIAL', 'orden' => 23],
            ['tipo' => 'E', 'campo' => 'barrio_notificacion', 'detalle' => 'BARRIO NOTIFICACIONES', 'orden' => 22],
            ['tipo' => 'E', 'campo' => 'cedrep', 'detalle' => 'DOCUMENTO REPRESENTANTE', 'orden' => 5],
            ['tipo' => 'E', 'campo' => 'celpri', 'detalle' => 'CELULAR COMERCIAL', 'orden' => 18],
            ['tipo' => 'E', 'campo' => 'celular', 'detalle' => 'CELULAR NOTIFICACIONES', 'orden' => 21],
            ['tipo' => 'E', 'campo' => 'ciupri', 'detalle' => 'CIUDAD COMERCIAL', 'orden' => 16],
            ['tipo' => 'E', 'campo' => 'codact', 'detalle' => 'ACTIVIDAD ECONOMICA', 'orden' => 19],
            ['tipo' => 'E', 'campo' => 'codciu', 'detalle' => 'CIUDAD DE NOTIFICACIONES', 'orden' => 17],
            ['tipo' => 'E', 'campo' => 'coddocrepleg', 'detalle' => 'TIPO DOCUMENTO REPRESENTANTE LEGAL', 'orden' => 11],
            ['tipo' => 'E', 'campo' => 'codsuc', 'detalle' => 'SUCURSAL', 'orden' => 20],
            ['tipo' => 'E', 'campo' => 'codzon', 'detalle' => 'CIUDAD DE ACTIVIDAD LABORAL', 'orden' => 18],
            ['tipo' => 'E', 'campo' => 'direccion', 'detalle' => 'DIRECCION NOTIFICACIONES', 'orden' => 2],
            ['tipo' => 'E', 'campo' => 'dirpri', 'detalle' => 'DIRECCIÓN COMERCIAL', 'orden' => 15],
            ['tipo' => 'E', 'campo' => 'email', 'detalle' => 'CORREO', 'orden' => 3],
            ['tipo' => 'E', 'campo' => 'emailpri', 'detalle' => 'EMAIL COMERCIAL', 'orden' => 19],
            ['tipo' => 'E', 'campo' => 'matmer', 'detalle' => 'MATRICULA MERCANTIL', 'orden' => 10],
            ['tipo' => 'E', 'campo' => 'nit', 'detalle' => 'NIT', 'orden' => 0],
            ['tipo' => 'E', 'campo' => 'priape', 'detalle' => 'PRIMER APELLIDO REPRESENTANTE', 'orden' => 15],
            ['tipo' => 'E', 'campo' => 'prinom', 'detalle' => 'PRIMER NOMBRE REPRESENTANTE', 'orden' => 13],
            ['tipo' => 'E', 'campo' => 'razsoc', 'detalle' => 'RAZON SOCIAL', 'orden' => 8],
            ['tipo' => 'E', 'campo' => 'repleg', 'detalle' => 'NOMBRE REPRESENTANTE', 'orden' => 6],
            ['tipo' => 'E', 'campo' => 'segape', 'detalle' => 'SEGUNDO APELLIDO REPRESENTANTE', 'orden' => 16],
            ['tipo' => 'E', 'campo' => 'segnom', 'detalle' => 'SEGUNDO NOMBRE REPRESENTANTE', 'orden' => 14],
            ['tipo' => 'E', 'campo' => 'sigla', 'detalle' => 'SIGLAS', 'orden' => 9],
            ['tipo' => 'E', 'campo' => 'telefono', 'detalle' => 'TELEFONO NOTIFICACIONES', 'orden' => 1],
            ['tipo' => 'E', 'campo' => 'telpri', 'detalle' => 'TELEFONO COMERCIAL', 'orden' => 17],
            ['tipo' => 'E', 'campo' => 'tipsoc', 'detalle' => 'TIPO SOCIEDAD', 'orden' => 12],

            // Campos para TRABAJADOR (T)
            ['tipo' => 'T', 'campo' => 'banco', 'detalle' => 'BANCO', 'orden' => 15],
            ['tipo' => 'T', 'campo' => 'celular', 'detalle' => 'CELULAR', 'orden' => 5],
            ['tipo' => 'T', 'campo' => 'codciu', 'detalle' => 'CIUDAD RESIDENCIA', 'orden' => 6],
            ['tipo' => 'T', 'campo' => 'codzon', 'detalle' => 'CIUDAD LABORAL', 'orden' => 7],
            ['tipo' => 'T', 'campo' => 'direccion', 'detalle' => 'DIRECCION', 'orden' => 1],
            ['tipo' => 'T', 'campo' => 'dirlab', 'detalle' => 'DIRECCION LABORAL', 'orden' => 8],
            ['tipo' => 'T', 'campo' => 'email', 'detalle' => 'EMAIL', 'orden' => 2],
            ['tipo' => 'T', 'campo' => 'expedicion', 'detalle' => 'FECHA EXPEDICIÓN DOCUMENTO', 'orden' => 13],
            ['tipo' => 'T', 'campo' => 'numcue', 'detalle' => 'CUENTA', 'orden' => 12],
            ['tipo' => 'T', 'campo' => 'priape', 'detalle' => 'PRIMER APELLIDO', 'orden' => 3],
            ['tipo' => 'T', 'campo' => 'prinom', 'detalle' => 'PRIMER NOMBRE', 'orden' => 1],
            ['tipo' => 'T', 'campo' => 'respo_banco', 'detalle' => 'RESPONSABLE BANCO', 'orden' => 15],
            ['tipo' => 'T', 'campo' => 'respo_celular', 'detalle' => 'RESPONSABLE CELULAR', 'orden' => 5],
            ['tipo' => 'T', 'campo' => 'respo_email', 'detalle' => 'RESPONSABLE EMAIL', 'orden' => 2],
            ['tipo' => 'T', 'campo' => 'respo_expedicion', 'detalle' => 'RESPONSABLE FECHA EXPEDICIÓN', 'orden' => 13],
            ['tipo' => 'T', 'campo' => 'respo_numcue', 'detalle' => 'RESPONSABLE CUENTA', 'orden' => 11],
            ['tipo' => 'T', 'campo' => 'respo_priape', 'detalle' => 'RESPONSABLE PRIMER APELLIDO', 'orden' => 3],
            ['tipo' => 'T', 'campo' => 'respo_prinom', 'detalle' => 'RESPONSABLE PRIMER NOMBRE', 'orden' => 1],
            ['tipo' => 'T', 'campo' => 'respo_segape', 'detalle' => 'RESPONSABLE SEGUNDO APELLIDO', 'orden' => 4],
            ['tipo' => 'T', 'campo' => 'respo_segnom', 'detalle' => 'RESPONSABLE SEGUNDO NOMBRE', 'orden' => 2],
            ['tipo' => 'T', 'campo' => 'respo_telefono', 'detalle' => 'RESPONSABLE TELEFONO', 'orden' => 4],
            ['tipo' => 'T', 'campo' => 'respo_tipdoc', 'detalle' => 'RESPONSABLE TIPO DOCUMENTO', 'orden' => 10],
            ['tipo' => 'T', 'campo' => 'segape', 'detalle' => 'SEGUNDO APELLIDO', 'orden' => 4],
            ['tipo' => 'T', 'campo' => 'segnom', 'detalle' => 'SEGUNDO NOMBRE', 'orden' => 2],
            ['tipo' => 'T', 'campo' => 'telefono', 'detalle' => 'TELEFONO', 'orden' => 4],
            ['tipo' => 'T', 'campo' => 'tipdoc', 'detalle' => 'TIPO DOCUMENTO', 'orden' => 10],
        ];

        foreach ($camposFormulario as $campo) {
            \App\Models\Mercurio28::updateOrCreate(
                ['tipo' => $campo['tipo'], 'campo' => $campo['campo']],
                $campo
            );
        }
    }

    /* INSERT INTO `mercurio28` VALUES ('E','barrio_comercial','BARRIO COMERCIAL',23),('E','barrio_notificacion','BARRIO NOTIFICACIONES',22),('E','cedrep','DOCUMENTO REPRESENTANTE',5),('E','celpri','CELULAR COMERCIAL',18),('E','celular','CELULAR NOTIFICACIONES',21),('E','ciupri','CIUDAD COMERCIAL',16),('E','codact','ACTIVIDAD ECONOMICA',19),('E','codciu','CIUDAD DE NOTIFICACIONES',17),('E','coddocrepleg','TIPO DOCUMENTO REPRESENTANTE LEGAL',11),('E','codsuc','SUCURSAL',20),('E','codzon','CIUDAD DE ACTIVIDAD LABORAL',18),('E','direccion','DIRECCION NOTIFICACIONES',2),('E','dirpri','DIRECCIÓN COMERCIAL',15),('E','email','CORREO',3),('E','emailpri','EMAIL COMERCIAL',19),('E','matmer','MATRICULA MERCANTIL',10),('E','nit','NIT',0),('E','priape','PRIMER APELLIDO REPRESENTANTE',15),('E','prinom','PRIMER NOMBRE REPRESENTANTE',13),('E','razsoc','RAZON SOCIAL',8),('E','repleg','NOMBRE REPRESENTANTE',6),('E','segape','SEGUNDO APELLIDO REPRESENTANTE',16),('E','segnom','SEGUNDO NOMBRE REPRESENTANTE',14),('E','sigla','SIGLAS',9),('E','telefono','TELEFONO NOTIFICACIONES',1),('E','telpri','TELEFONO COMERCIAL',17),('E','tipsoc','TIPO SOCIEDAD',12),('T','banco','BANCO',15),('T','celular','CELULAR',5),('T','codciu','CIUDAD RESIDENCIA',6),('T','codzon','CIUDAD LABORAL',7),('T','direccion','DIRECCION',1),('T','dirlab','DIRECCION LABORAL',8),('T','email','EMAIL',2),('T','expedicion','FECHA EXPEDICIÓN DOCUMENTO',13),('T','numcue','CUENTA',12),('T','priape','PRIMER APELLIDO',3),('T','prinom','PRIMER NOMBRE',1),('T','respo_banco','RESPONSABLE BANCO',15),('T','respo_celular','RESPONSABLE CELULAR',5),('T','respo_email','RESPONSABLE EMAIL',2),('T','respo_expedicion','RESPONSABLE FECHA EXPEDICIÓN',13),('T','respo_numcue','RESPONSABLE CUENTA',11),('T','respo_priape','RESPONSABLE PRIMER APELLIDO',3),('T','respo_prinom','RESPONSABLE PRIMER NOMBRE',1),('T','respo_segape','RESPONSABLE SEGUNDO APELLIDO',4),('T','respo_segnom','RESPONSABLE SEGUNDO NOMBRE',2),('T','respo_telefono','RESPONSABLE TELEFONO',1),('T','respo_tipcuenta','RESPONSABLE TIPO DE CUENTA',14),('T','respo_tippag','RESPONSABLE TIPO PAGO',10),('T','segape','SEGUNDO APELLIDO',4),('T','segnom','SEGUNDO NOMBRE',2),('T','telefono','TELEFONO',1),('T','tipcuenta','TIPO DE CUENTA',14),('T','tippag','TIPO PAGO',10); */
}
