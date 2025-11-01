<?php

namespace App\Services\Formularios\Generation;

use App\Exceptions\DebugException;

// Factory para generación local (formularios)
use App\Services\Formularios\FactoryDocuments;

// API
use App\Services\Formularios\Api\TrabajadoresDocuments;
use App\Services\Formularios\Api\EmpresasDocuments;
use App\Services\Formularios\Api\BeneficiariosDocuments;
use App\Services\Formularios\Api\IndependientesDocuments;
use App\Services\Formularios\Api\PensionadosDocuments;
use App\Services\Formularios\Api\FacultativosDocuments;
use App\Services\Formularios\Api\ConyugesDocuments;
use App\Services\Formularios\Api\ActualizadatosDocuments;

class DocumentGenerationManager
{
    private array $map = [
        // Canal local: validación de tipos soportados por FactoryDocuments->crearFormulario
        'local' => [
            'trabajador' => true,
            'pensionado' => true,
            'facultativo' => true,
            'independiente' => true,
            'empresa' => true,
            'conyuge' => true,
            'beneficiario' => true,
            'actualizadatos' => true,
        ],
        'api' => [
            'trabajador' => TrabajadoresDocuments::class,
            'pensionado' => PensionadosDocuments::class,
            'facultativo' => FacultativosDocuments::class,
            'independiente' => IndependientesDocuments::class,
            'empresa' => EmpresasDocuments::class,
            'conyuge' => ConyugesDocuments::class,
            'beneficiario' => BeneficiariosDocuments::class,
            'actualizadatos' => ActualizadatosDocuments::class,
        ],
    ];

    public function getGenerator(string $canal, string $tipo, array $params = [])
    {
        $canal = strtolower(trim($canal));
        $tipo = strtolower(trim($tipo));

        if (!isset($this->map[$canal])) {
            throw new DebugException("Canal no soportado {$canal}");
        }
        // Para canal local, la validación detallada la delegamos a FactoryDocuments según la categoría
        if ($canal !== 'local' && !isset($this->map[$canal][$tipo])) {
            throw new DebugException("Tipo de documento no soportado {$tipo} para canal {$canal}");
        }

        // Delegación según canal
        if ($canal === 'local') {
            // Usar la fábrica existente para todas las categorías locales
            // categorias soportadas por FactoryDocuments: formulario|oficio|politica|declaracion
            $categoria = strtolower(trim($params['categoria'] ?? 'formulario'));
            $factory = new FactoryDocuments();
            switch ($categoria) {
                case 'formulario':
                    return $factory->crearFormulario($tipo);
                case 'oficio':
                    return $factory->crearOficio($tipo);
                case 'politica':
                    return $factory->crearPolitica($tipo);
                case 'declaracion':
                    return $factory->crearDeclaracion($tipo);
                default:
                    throw new DebugException("Categoría local no soportada {$categoria}");
            }
        }

        // Canal API: instanciación directa de la clase mapeada
        $class = $this->map[$canal][$tipo];
        return new $class();
    }

    public function generate(string $canal, string $tipo, array $params)
    {
        $generator = $this->getGenerator($canal, $tipo, $params);
        // Ambos mundos exponen setParamsInit y main, por lo que no requerimos adaptadores
        $generator->setParamsInit($params);
        return $generator->main();
    }

    /**
     * Procesa múltiples documentos de un mismo canal.
     * Cada item debe tener: ['tipo' => string, 'params' => array]
     * Retorna un arreglo indexado con el resultado de cada generación en orden.
     */
    public function generateMany(string $canal, array $items)
    {
        $results = [];
        foreach ($items as $idx => $item) {
            $tipo = $item['tipo'] ?? null;
            $params = $item['params'] ?? [];
            if (!$tipo || !is_array($params)) {
                $results[$idx] = [
                    'success' => false,
                    'error' => 'Item inválido: requiere keys tipo (string) y params (array)'
                ];
                continue;
            }

            try {
                $out = $this->generate($canal, $tipo, $params);
                $results[$idx] = [
                    'success' => true,
                    'result' => $out,
                ];
            } catch (\Throwable $e) {
                $results[$idx] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
