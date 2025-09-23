<?php

namespace Database\Seeders;

use App\Models\Mercurio14;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Mercurio14Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentosOperacion = [
            ['tipopc' => '10', 'tipsoc' => '00', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario, para afiliación con firma digital.'],
            ['tipopc' => '10', 'tipsoc' => '00', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '10', 'tipsoc' => '00', 'coddoc' => 4, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Declaración juramentada facultativo'],
            ['tipopc' => '10', 'tipsoc' => '08', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario, para afiliación con firma digital.'],
            ['tipopc' => '10', 'tipsoc' => '08', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '10', 'tipsoc' => '08', 'coddoc' => 24, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado para afiliación a la Caja de Compensación Familiar Del Caquetá .'],
            ['tipopc' => '10', 'tipsoc' => '08', 'coddoc' => 25, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generada, de la política de tratamiento de datos personales para afiliación a la Caja de Compensación Familiar Del Caquetá. '],
            ['tipopc' => '12', 'tipsoc' => '00', 'coddoc' => 1, 'obliga' => 'N', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario, para afiliación con firma digital.'],
            ['tipopc' => '12', 'tipsoc' => '00', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '12', 'tipsoc' => '00', 'coddoc' => 8, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Certificado RUT, donde indique la actividad económica principal y con fecha vigente.'],
            ['tipopc' => '12', 'tipsoc' => '00', 'coddoc' => 24, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado para afiliación a la Caja de Compensación Familiar Del Caquetá .'],
            ['tipopc' => '12', 'tipsoc' => '00', 'coddoc' => 25, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generada, de la política de tratamiento de datos personales para afiliación a la Caja de Compensación Familiar Del Caquetá.'],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario trabajador independiente, para afiliación con firma digital.'],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => null],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 4, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Declaración juramentada independiente '],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 6, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Oficio certificado por la entidad EPS, donde se relacione el afiliado.'],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 8, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Certificado RUT, donde indique la actividad económica principal y con fecha vigente.'],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 15, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la planilla de Seguridad Social, que emite la entidad pagadora de los aportes PILA. '],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 24, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado para afiliación a la Caja de Compensación Familiar Del Caquetá .'],
            ['tipopc' => '13', 'tipsoc' => '08', 'coddoc' => 25, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generada, de la política de tratamiento de datos personales para afiliación a la Caja de Compensación Familiar Del Caquetá.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario, para afiliación con firma digital.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 8, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Certificado RUT, donde indique la actividad económica principal y con fecha vigente.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 9, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Certificado de cámara y comercio actualizado y vigente.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 10, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Un consorcio o unión temporal, una "Acta Consorcial" o "Acta de Conformación" es un documento que formaliza el acuerdo entre dos o más personas (naturales o jurídicas) para colaborar en la ejecución de un proyecto o actividad específica.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 11, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio autogenerado donde se relacionan los trabajadores en la nómina del empleador.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 24, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Oficio auto-generado para afiliación a la Caja de Compensación Familiar Del Caquetá .'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 25, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Oficio auto-generada, de la política de tratamiento de datos personales para afiliación a la Caja de Compensación Familiar Del Caquetá.'],
            ['tipopc' => '2', 'tipsoc' => '00', 'coddoc' => 26, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Documento emitido por la Caja de Compensación a la que estuvo afiliado el empleador.'],
            ['tipopc' => '3', 'tipsoc' => '00', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario adición cónyuge, para afiliación con firma digital.'],
            ['tipopc' => '3', 'tipsoc' => '00', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '3', 'tipsoc' => '00', 'coddoc' => 4, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario adición cónyuge, para afiliación con firma digital.'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 3, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia del Registro Civil de la persona cónyuge del trabajador afiliado.'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 4, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 6, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Oficio certificado por la entidad EPS, donde se relacione el afiliado.'],
            ['tipopc' => '3', 'tipsoc' => '07', 'coddoc' => 7, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Copia de documento oficial donde se asigna la responsabilidad y autoridad legal con las firmas respectivas.'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario adición beneficiario, para afiliación con firma digital.'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 3, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia del Registro Civil de identificación del menor.'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 4, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 6, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Oficio certificado por la entidad EPS, donde se relacione el afiliado.'],
            ['tipopc' => '4', 'tipsoc' => '00', 'coddoc' => 7, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Copia de documento oficial donde se asigna la responsabilidad y autoridad legal con las firmas respectivas.'],
            ['tipopc' => '4', 'tipsoc' => '07', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario adición beneficiario, para afiliación con firma digital.'],
            ['tipopc' => '4', 'tipsoc' => '07', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '5', 'tipsoc' => '06', 'coddoc' => 2, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '5', 'tipsoc' => '06', 'coddoc' => 8, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Certificado RUT, donde indique la actividad económica principal y con fecha vigente.'],
            ['tipopc' => '5', 'tipsoc' => '06', 'coddoc' => 9, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Certificado de cámara y comercio actualizado y vigente.'],
            ['tipopc' => '5', 'tipsoc' => '06', 'coddoc' => 10, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Un consorcio o unión temporal, una "Acta Consorcial" o "Acta de Conformación" es un documento que formaliza el acuerdo entre dos o más personas (naturales o jurídicas) para colaborar en la ejecución de un proyecto o actividad específica.'],
            ['tipopc' => '5', 'tipsoc' => '06', 'coddoc' => 27, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Oficio autogenerado para actualizar datos del empleador.'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 1, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado Formulario pensionado, para afiliación con firma digital.'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 2, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Copia de la cédula o cualquier otro tipo de documento de identificación'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 4, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Declaración juramentada pensionado'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 24, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generado para afiliación a la Caja de Compensación Familiar Del Caquetá .'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 25, 'obliga' => 'S', 'auto_generado' => 1, 'nota' => 'Oficio auto-generada, de la política de tratamiento de datos personales para afiliación a la Caja de Compensación Familiar Del Caquetá.'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 33, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'Un certificado o copia de la resolución de pensión es un documento oficial que válida que una persona tiene derecho a una pensión y que especifica los detalles de dicha pensión, como su valor, vigencia y otras condiciones'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 34, 'obliga' => 'S', 'auto_generado' => 0, 'nota' => 'La "colilla de pago última mesada pensional" es un documento que detalla el pago de tu pensión, incluyendo los valores devengados, las deducciones realizadas y el monto neto que recibes al final. En otras palabras, es un comprobante de pago o un extracto.'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 35, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Copia de los documentos de identificación de todos los hijos e hijastros del afiliado pensionado.'],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 36, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Copia del documento de identificación de la persona cónyuge del afiliado pensionado. '],
            ['tipopc' => '9', 'tipsoc' => '08', 'coddoc' => 37, 'obliga' => 'N', 'auto_generado' => 0, 'nota' => 'Documento emitido por la Caja de Compensación Familiar Del Caquetá, que certifica el contar con más de 25 años de estar afiliado a la misma. '],
        ];

        foreach ($documentosOperacion as $documento) {
            Mercurio14::updateOrCreate(
                [
                    'tipopc' => $documento['tipopc'],
                    'tipsoc' => $documento['tipsoc'],
                    'coddoc' => $documento['coddoc']
                ],
                $documento
            );
        }
    }
}
