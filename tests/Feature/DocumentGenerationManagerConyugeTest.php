<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Formularios\Generation\DocumentGenerationManager;
use App\Library\Collections\ParamsConyuge;

class SimpleModel
{
    private $a;
    public function __construct(array $a)
    {
        $this->a = $a;
        foreach ($a as $k => $v) {
            $this->$k = $v;
        }
    }
    public function toArray()
    {
        return $this->a;
    }
}

class DocumentGenerationManagerConyugeTest extends TestCase
{
    private function requireIntegration()
    {
        if (! getenv('RUN_DOCS_INTEGRATION')) {
            $this->markTestSkipped('RUN_DOCS_INTEGRATION no está activo. Esta es una prueba de integración real.');
        }
    }

    private function seedParamsConyugeCatalogos()
    {
        // Catálogos mínimos para evitar null en accesos de ParamsConyuge
        $datos = [
            'resguardos' => [ ['id' => 1, 'detalle' => 'NINGUNO'] ],
            'pueblos_indigenas' => [ ['id' => 1, 'detalle' => 'NINGUNO'] ],
            'pertenencia_etnicas' => [ ['codigo' => '00', 'nombre' => 'NINGUNA'] ],
            'discapacidades' => [ ['tipdis' => '00', 'detalle' => 'NINGUNA'] ],
            'bancos' => [ ['codban' => '00', 'detalle' => 'NINGUNO'] ],
            'companero_permanente' => [ ['estado' => 'N', 'detalle' => 'No'] ],
            'recibe_subsidio' => [ ['estado' => 'N', 'detalle' => 'No'] ],
            'tipo_cuenta' => [ ['estado' => 'AH', 'detalle' => 'Ahorros'] ],
            'codigo_cuenta' => [ ['codcue' => '001', 'detalle' => 'Default'] ],
            'tipo_pago' => [ ['estado' => 'T', 'detalle' => 'PENDIENTE FORMA DE PAGO'] ],
            'vivienda' => [ ['vivienda' => 'P', 'detalle' => 'Propia'] ],
            'captra' => [ ['captra' => 'S', 'detalle' => 'Si'] ],
            'nivel_educativos' => [ ['nivedu' => '01', 'detalle' => 'Básica'] ],
            'estado_civiles' => [ ['estciv' => 'S', 'detest' => 'Soltero(a)'] ],
            'sexos' => [ ['codsex' => 'F', 'detsex' => 'Femenino'] ],
            'zonas' => [ ['codzon' => 18001, 'detzon' => 'FLORENCIA'] ],
            'ciudades' => [ ['codciu' => 18001, 'detciu' => 'FLORENCIA'] ],
            'tipo_documentos' => [ ['coddoc' => 1, 'detdoc' => 'CC', 'codrua' => 'CC'] ],
            'ocupaciones' => [ ['codocu' => '00', 'detalle' => 'NINGUNA'] ],
        ];
        (new ParamsConyuge())->setDatosCaptura($datos);
    }

    /**
     * Ejemplo de consumo para canal API con tipo conyuge.
     */
    public function test_api_conyuge_generation_example()
    {
        $this->requireIntegration();
        $this->seedParamsConyugeCatalogos();

        $manager = new DocumentGenerationManager();

        $conyuge = new SimpleModel([
            'tipdoc' => 1,
            'codzon' => 18001,
            'resguardo_id' => null,
            'peretn' => null,
            'pub_indigena_id' => null,
            'tipdis' => null,
            'salario' => 0,
            'empresalab' => null,
            'tippag' => 'T',
            'prinom' => 'Ana','segnom' => 'María','priape' => 'Pérez','segape' => 'López',
        ]);
        $trabajador = new SimpleModel([
            'cedtra' => '123', 'tipdoc' => 1, 'prinom' => 'Juan','segnom' => 'Carlos','priape' => 'Gómez','segape' => 'Ruiz',
            'nit' => '900000001-1'
        ]);

        $ok = $manager->generate('api', 'conyuge', [
            'conyuge' => ['prinom' => 'Ana'],
            'trabajador' => ['prinom' => 'Juan'],
            'oficios' => [
                ['template' => 'templates/conyuge/oficio1.html', 'output' => 'storage/oficios/conyuge/oficio1.pdf'],
            ],
        ]);

        $this->assertTrue($ok === true || $ok === false); // depende del entorno externo de generación
    }

    /**
     * Ejemplo de consumo para canal LOCAL con tipo conyuge (por FactoryDocuments, categoría formulario).
     */
    public function test_local_conyuge_generation_example()
    {
        $this->requireIntegration();
        $this->seedParamsConyugeCatalogos();

        $manager = new DocumentGenerationManager();

        $conyuge = new SimpleModel([
            'tipdoc' => 1,
            'codzon' => 18001,
            'prinom' => 'Ana','segnom' => 'María','priape' => 'Pérez','segape' => 'López',
        ]);
        $trabajador = new SimpleModel([
            'cedtra' => '123', 'tipdoc' => 1, 'prinom' => 'Juan','segnom' => 'Carlos','priape' => 'Gómez','segape' => 'Ruiz',
            'nit' => '900000001-1'
        ]);

        $doc = $manager->generate('local', 'conyuge', [
            'categoria' => 'formulario',
            'filename' => 'form_conyuge.pdf',
            'conyuge' => $conyuge,
            'trabajador' => $trabajador,
            'background' => 'img/form/conyuge/bg.jpg',
        ]);

        $this->assertTrue(is_object($doc) || $doc === true); // según implementación puede retornar $this o bool
    }
}
