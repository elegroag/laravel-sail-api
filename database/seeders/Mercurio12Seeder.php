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
                'detalle' => 'Formulario',
            ],
            [
                'coddoc' => 2,
                'detalle' => 'Documento Identificación',
            ],
            [
                'coddoc' => 3,
                'detalle' => 'Registro Civil',
            ],
            [
                'coddoc' => 4,
                'detalle' => 'Declaración Juramentada',
            ],
            [
                'coddoc' => 5,
                'detalle' => 'Tarjeta de Identidad',
            ],
            [
                'coddoc' => 6,
                'detalle' => 'Certificado de EPS',
            ],
            [
                'coddoc' => 7,
                'detalle' => 'Custodia Legal',
            ],
            [
                'coddoc' => 8,
                'detalle' => 'RUT',
            ],
            [
                'coddoc' => 9,
                'detalle' => 'Camara de Comercio',
            ],
            [
                'coddoc' => 10,
                'detalle' => 'Acta Consorcial o Conformación',
            ],
            [
                'coddoc' => 11,
                'detalle' => 'Relación Trabajadores en Nomina',
            ],
            [
                'coddoc' => 12,
                'detalle' => 'Estatutos de Cooperativa',
            ],
            [
                'coddoc' => 13,
                'detalle' => 'Resolución Ministerio Trabajado para Cooperativa',
            ],
            [
                'coddoc' => 14,
                'detalle' => 'Certificación Ingresos U OPS',
            ],
            [
                'coddoc' => 15,
                'detalle' => 'Planilla Seguridad Social',
            ],
            [
                'coddoc' => 17,
                'detalle' => 'Permiso Especial Extranjeria',
            ],
            [
                'coddoc' => 18,
                'detalle' => 'Certificado de Discapacidad',
            ],
            [
                'coddoc' => 19,
                'detalle' => 'Certificado Bancario o Daviplata Cónyuges',
            ],
            [
                'coddoc' => 21,
                'detalle' => 'Certificado de Muerte Padre o Madre',
            ],
            [
                'coddoc' => 22,
                'detalle' => 'Permiso de Trabajo Menor de Edad',
            ],
            [
                'coddoc' => 23,
                'detalle' => 'Certificado Escolar',
            ],
            [
                'coddoc' => 24,
                'detalle' => 'Oficio Solicitud Afiliación a Comfaca',
            ],
            [
                'coddoc' => 25,
                'detalle' => 'Tratamiento Datos Personales',
            ],
            [
                'coddoc' => 26,
                'detalle' => 'Paz y Salvo Caja Anterior',
            ],
            [
                'coddoc' => 27,
                'detalle' => 'Formulario de Actualización',
            ],
            [
                'coddoc' => 28,
                'detalle' => 'Disolución Sociedad Conyugal',
            ],
            [
                'coddoc' => 29,
                'detalle' => 'Declaración Extrajuicio DIS - Conyugal',
            ],
            [
                'coddoc' => 30,
                'detalle' => 'Certificado de Cuenta Banco o Daviplata',
            ],
            [
                'coddoc' => 31,
                'detalle' => 'Copia OPS o Certificado de Ingresos emitido por un Contador',
            ],
            [
                'coddoc' => 32,
                'detalle' => 'Copia Documento Identificación del Contador',
            ],
            [
                'coddoc' => 33,
                'detalle' => 'Certificado o Copia Resolución de Pensión',
            ],
            [
                'coddoc' => 34,
                'detalle' => 'Colilla de Pago Última Mesada Pensional',
            ],
            [
                'coddoc' => 35,
                'detalle' => 'Documentos de Identificación Hijos del Pensionado',
            ],
            [
                'coddoc' => 36,
                'detalle' => 'Documento Identificación Cónyuge del Pensionado',
            ],
            [
                'coddoc' => 37,
                'detalle' => 'Certificado 25 Años Afiliado a Comfaca o Historial Laboral',
            ],
            [
                'coddoc' => 38,
                'detalle' => 'Certificado Fondo de Pensiones',
            ],
            [
                'coddoc' => 39,
                'detalle' => 'RUT Empleador - Aplica Servicio Doméstico',
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
