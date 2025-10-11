<?php

namespace App\Services\ReportGenerator\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface IReportProduct
{
    /**
     * Establece los datos del reporte utilizando un PHP Generator para eficiencia.
     * El generador debe emitir primero un arreglo de encabezados y luego las filas de datos.
     */
    public function setData(\Generator $data): void;

    /**
     * Genera el contenido binario o de texto del reporte.
     */
    public function generateContent(): string;

    /**
     * Retorna una respuesta con streaming que el controlador debe devolver.
     */
    public function streamOutput(string $filename): StreamedResponse;
}
