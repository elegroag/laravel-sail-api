<?php

namespace Database\Seeders;

use App\Models\Mercurio12;
use Illuminate\Database\Seeder;

class Mercurio12Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codigosDocumentos = [
            [
                'coddoc' => 1,
                'detalle' => 'FORMULARIO',
            ],
            [
                'coddoc' => 2,
                'detalle' => 'DOCUMENTO IDENTIFICACIÓN',
            ],
            [
                'coddoc' => 3,
                'detalle' => 'REGISTRO CIVIL',
            ],
            [
                'coddoc' => 4,
                'detalle' => 'DECLARACIÓN JURAMENTADA',
            ],
            [
                'coddoc' => 5,
                'detalle' => 'TARJETA DE IDENTIDAD',
            ],
            [
                'coddoc' => 6,
                'detalle' => 'CERTIFICADO DE EPS',
            ],
            [
                'coddoc' => 7,
                'detalle' => 'CUSTODIA LEGAL',
            ],
            [
                'coddoc' => 8,
                'detalle' => 'RUT',
            ],
            [
                'coddoc' => 9,
                'detalle' => 'CAMARA DE COMERCIO',
            ],
            [
                'coddoc' => 10,
                'detalle' => 'ACTA CONSORCIAL O CONFORMACIÓN',
            ],
            [
                'coddoc' => 11,
                'detalle' => 'RELACION TRABAJADORES EN NOMINA',
            ],
            [
                'coddoc' => 12,
                'detalle' => 'ESTATUTOS DE COOPERATIVA',
            ],
            [
                'coddoc' => 13,
                'detalle' => 'RESOLUCION MINISTERIO TRABAJADO PARA COOPERATIVA',
            ],
            [
                'coddoc' => 14,
                'detalle' => 'CERTIFICACION INGRESOS U OPS',
            ],
            [
                'coddoc' => 15,
                'detalle' => 'PLANILLA SEGURIDAD SOCIAL',
            ],
            [
                'coddoc' => 17,
                'detalle' => 'PERMISO ESPECIAL EXTRANJERIA',
            ],
            [
                'coddoc' => 18,
                'detalle' => 'CERTIFICADO DE DISCAPACIDAD',
            ],
            [
                'coddoc' => 19,
                'detalle' => 'CERTIFICADO BANCARIO O DAVIPLATA CÓNYUGES',
            ],
            [
                'coddoc' => 21,
                'detalle' => 'CERTIFICADO DE MUERTE PADRE O MADRE',
            ],
            [
                'coddoc' => 22,
                'detalle' => 'PERMISO DE TRABAJO MENOR DE EDAD',
            ],
            [
                'coddoc' => 23,
                'detalle' => 'CERTIFICADO ESCOLAR',
            ],
            [
                'coddoc' => 24,
                'detalle' => 'OFICIO SOLICITUD AFILIACIÓN A COMFACA',
            ],
            [
                'coddoc' => 25,
                'detalle' => 'TRATAMIENTO DATOS PERSONALES',
            ],
            [
                'coddoc' => 26,
                'detalle' => 'PAZ Y SALVO CAJA ANTERIOR',
            ],
            [
                'coddoc' => 27,
                'detalle' => 'FORMULARIO DE ACTUALIZACIÓN',
            ],
            [
                'coddoc' => 28,
                'detalle' => 'DISOLUCIÓN SOCIEDAD CONYUGAL',
            ],
            [
                'coddoc' => 29,
                'detalle' => 'DECLARACIÓN EXTRAJUICIO DIS - CONYUGAL',
            ],
            [
                'coddoc' => 30,
                'detalle' => 'CERTIFICADO DE CUENTA BANCO O DAVIPLATA',
            ],
            [
                'coddoc' => 31,
                'detalle' => 'COPIA OPS O CERTIFICADO DE INGRESOS EMITIDO POR UN CONTADOR',
            ],
            [
                'coddoc' => 32,
                'detalle' => 'COPIA DOCUMENTO IDENTIFICACIÓN DEL CONTADOR',
            ],
            [
                'coddoc' => 33,
                'detalle' => 'CERTIFICADO O COPIA RESOLUCIÓN DE PENSIÓN',
            ],
            [
                'coddoc' => 34,
                'detalle' => 'COLILLA DE PAGO ÚLTIMA MESADA PENSIONAL',
            ],
            [
                'coddoc' => 35,
                'detalle' => 'DOCUMENTOS DE IDENTIFICACIÓN HIJOS DEL PENSIONADO',
            ],
            [
                'coddoc' => 36,
                'detalle' => 'DOCUMENTO IDENTIFICACIÓN CÓNYUGE DEL PENSIONADO',
            ],
            [
                'coddoc' => 37,
                'detalle' => 'CERTIFICADO 25 AÑOS AFILIADO A COMFACA O HISTORIAL LABORAL',
            ],
            [
                'coddoc' => 38,
                'detalle' => 'CERTIFICADO FONDO DE PENSIÓNES',
            ],
            [
                'coddoc' => 39,
                'detalle' => 'RUT EMPLEADOR - APLICA SERVICIO DOMESTICO',
            ],
        ];

        foreach ($codigosDocumentos as $documento) {
            Mercurio12::updateOrCreate(
                ['coddoc' => $documento['coddoc']],
                $documento
            );
        }
    }

    /* INSERT INTO `mercurio12` VALUES (1,'FORMULARIO'),(2,'DOCUMENTO IDENTIFICACIÓN'),(3,'REGISTRO CIVIL'),(4,'DECLARACIÓN JURAMENTADA'),(5,'TARJETA DE IDENTIDAD'),(6,'CERTIFICADO DE EPS'),(7,'CUSTODIA LEGAL'),(8,'RUT'),(9,'CAMARA DE COMERCIO'),(10,'ACTA CONSORCIAL O CONFORMACIÓN'),(11,'RELACION TRABAJADORES EN NOMINA'),(12,'ESTATUTOS DE COOPERATIVA'),(13,'RESOLUCION MINISTERIO TRABAJADO PARA COOPERATIVA'),(14,'CERTIFICACION INGRESOS U OPS'),(15,'PLANILLA SEGURIDAD SOCIAL'),(17,'PERMISO ESPECIAL EXTRANJERIA'),(18,'CERTIFICADO DE DISCAPACIDAD'),(19,'CERTIFICADO  BANCARIO O DAVIPLATA CÓNYUGES'),(21,'CERTIFICADO DE MUERTE PADRE O MADRE'),(22,'PERMISO DE TRABAJO MENOR DE EDAD'),(23,'CERTIFICADO ESCOLAR'),(24,'OFICIO SOLICITUD AFILIACIÓN A COMFACA'),(25,'TRATAMIENTO DATOS PERSONALES'),(26,'PAZ Y SALVO CAJA ANTERIOR'),(27,'FORMULARIO DE ACTUALIZACIÓN'),(28,'DISOLUCIÓN SOCIEDAD CONYUGAL'),(29,'DECLARACIÓN EXTRAJUICIO DIS - CONYUGAL'),(30,'CERTIFICADO DE CUENTA BANCO O DAVIPLATA'),(31,'COPIA OPS O CERTIFICADO DE INGRESOS EMITIDO POR UN CONTADOR'),(32,'COPIA DOCUMENTO IDENTIFICACIÓN DEL CONTADOR'),(33,'CERTIFICADO  O COPIA RESOLUCIÓN DE PENSIÓN'),(34,'COLILLA DE PAGO ÚLTIMA MESADA PENSIONAL'),(35,'DOCUMENTOS DE IDENTIFICACIÓN HIJOS DEL PENSIONADO'),(36,'DOCUMENTO IDENTIFICACIÓN CÓNYUGE DEL PENSIONADO'),(37,'CERTIFICADO 25 AÑOS AFILIADO A COMFACA O HISTORIAL LABORAL'),(38,'CERTIFICADO FONDO DE PENSIÓNES'),(39,'RUT EMPLEADOR - APLICA SERVICIO DOMESTICO'); */
}
