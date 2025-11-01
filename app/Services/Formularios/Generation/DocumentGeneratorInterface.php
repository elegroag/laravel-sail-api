<?php

namespace App\Services\Formularios\Generation;

interface DocumentGeneratorInterface
{
    /**
     * Inicializa parámetros necesarios para generar el documento
     *
     * @param array $params
     * @return void
     */
    public function setParamsInit($params);

    /**
     * Ejecuta la generación del documento
     *
     * @return mixed
     */
    public function main();
}
