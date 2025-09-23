<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mercurio13;

class Mercurio13Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentosRequeridos = [
            [
                'tipopc' => '1',
                'coddoc' => 1,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 2,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 4,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 17,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 22,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 25,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 26,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 28,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 29,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 30,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '1',
                'coddoc' => 39,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '10',
                'coddoc' => 1,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '14',
                'coddoc' => 6,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '14',
                'coddoc' => 7,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '14',
                'coddoc' => 30,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '3',
                'coddoc' => 1,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '3',
                'coddoc' => 2,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '3',
                'coddoc' => 4,
                'obliga' => 'N',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '3',
                'coddoc' => 17,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '3',
                'coddoc' => 19,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 1,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 2,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 3,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 4,
                'obliga' => 'N',
                'auto_generado' => 1,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 5,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 6,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 7,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 17,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 18,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 21,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '4',
                'coddoc' => 23,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '5',
                'coddoc' => 4,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '5',
                'coddoc' => 27,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '5',
                'coddoc' => 28,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '5',
                'coddoc' => 29,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '5',
                'coddoc' => 30,
                'obliga' => 'N',
                'auto_generado' => 0,
            ],
            [
                'tipopc' => '9',
                'coddoc' => 1,
                'obliga' => 'S',
                'auto_generado' => 1,
            ],
        ];

        foreach ($documentosRequeridos as $documento) {
            Mercurio13::updateOrCreate(
                [
                    'tipopc' => $documento['tipopc'],
                    'coddoc' => $documento['coddoc'],
                ],
                $documento
            );
        }
    }

    /* INSERT INTO `mercurio13` VALUES ('1',1,'S',1,'Formulario auto-generado para afiliación de trabajadores.'),('1',2,'N',0,'Copia de la cédula o cualquier otro tipo de documento de identificación'),('1',4,'N',0,'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'),('1',17,'N',0,'Copia del permiso especial de extranjería debidamente firmado y vigente.'),('1',22,'N',0,'Oficio de permiso de trabajo del menor, con las firmas de los responsables y el respaldo jurídico.  '),('1',25,'S',1,'Política de tratamiento de datos de la Caja de Compensación Familiar de Caquetá.'),('1',26,'N',0,'El paz y salvo en caso de que la persona hubiera estado afiliado como trabajador independiente. Y demuestre el estar al día en el pago de los aportes PILA.'),('1',28,'N',0,'Copia del documento oficial donde se establece la separación del cónyuge, con las firmas respectivas.'),('1',29,'N',0,'Copia del documento oficial donde se establece la separación del cónyuge, con las firmas respectivas.'),('1',30,'N',0,'Certificado de Cuenta Bancaria, o Certificado de cuenta Daviplata, para consignación de la cuota monetaria, en caso de tener derecho a tal beneficio. '),('1',39,'N',0,'Certificado RUT, del empleador, donde indique que puede disponer de servicio doméstico. En caso de no presentar el documento, el trabajador se afilia bajo la actividad económica principal.'),('10',1,'S',1,'Formulario de afiliación para Facultativos'),('14',6,'N',0,'Oficio certificado por la entidad EPS, donde se relacione el beneficiario.'),('14',7,'N',0,'Copia de documento oficial donde se asigna la responsabilidad y autoridad legal con las firmas respectivas.'),('14',30,'N',0,'Certificado de Cuenta Bancaria, o Certificado de cuenta Daviplata, para consignación de la cuota monetaria, en caso de tener derecho a tal beneficio.'),('3',1,'S',1,'Formulario auto-generado para afiliación de la persona conyuge.'),('3',2,'N',0,'Copia de la cédula o cualquier otro tipo de documento de identificación'),('3',4,'N',1,'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'),('3',17,'N',0,'Copia del permiso especial de extranjería debidamente firmado y vigente.'),('3',19,'N',0,'Certificado de Cuenta Bancaria, o Certificado de cuenta Daviplata, para consignación de la cuota monetaria, en caso de tener derecho a tal beneficio. '),('4',1,'S',1,'Formulario auto-generado para afiliación del beneficiario.'),('4',2,'N',0,'Copia de la cédula o cualquier otro tipo de documento de identificación'),('4',3,'N',0,'Copia de documento de identificación, Registro Civil  del beneficiario menor.'),('4',4,'N',1,'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'),('4',5,'N',0,'Copia de la Tarjeta De Identidad, o cualquier otro tipo de documento de identificación del menor.'),('4',6,'N',0,'Oficio certificado por la entidad EPS, donde se relacione el beneficiario.'),('4',7,'N',0,'Copia de documento oficial donde se asigna la responsabilidad y autoridad legal con las firmas respectivas.'),('4',17,'N',0,'Copia del permiso especial de extranjería debidamente firmado y vigente.'),('4',18,'N',0,'Copia vigente del certificado de discapacidad, emitido por la EPS.'),('4',21,'N',0,'Documento certificado donde se demuestra el fallecimiento del trabajador (PADRE o MADRE).'),('4',23,'N',0,'Documento certificado escolar vigente del menor. Donde se relacione la institución, el grado cursando y la identificación del  menor.'),('5',4,'N',0,'Oficio por escrito y firmado donde se declare que la persona hace parte del núcleo familiar.'),('5',27,'N',0,'Oficio auto-generado para actualización de datos del empleador. '),('5',28,'N',0,'Copia del documento oficial donde se establece la separación del cónyuge, con las firmas respectivas.'),('5',29,'N',0,'Copia del documento oficial donde se establece la separación del cónyuge, con las firmas respectivas.'),('5',30,'N',0,'Certificado de Cuenta Bancaria, o Certificado de cuenta Daviplata, para consignación de la cuota monetaria, en caso de tener derecho a tal beneficio.'),('9',1,'S',1,'Formulario de afiliación de pensionados'); */
}
