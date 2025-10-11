<?php

namespace App\Services\ReportGenerator\Contracts;

interface IReportProduct
{
    /**
     * Establece los datos del reporte utilizando un PHP Generator para eficiencia.
     */
    public function setData(\Generator $data): void;

    /**
     * Genera el contenido binario o de texto del reporte.
     */
    public function generateContent(): string;

    /**
     * Envía las cabeceras HTTP y realiza el streaming al cliente.
     */
    public function streamOutput(string $filename): void;
}
