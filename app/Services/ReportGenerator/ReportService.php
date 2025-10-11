<?php

namespace App\Services\ReportGenerator;

use App\Services\ReportGenerator\Contracts\IReportFactory;
use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    private IReportFactory $factory;

    /**
     * El constructor recibe la fábrica a través de la inyección de dependencia (DI).
     */
    public function __construct(IReportFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Crea y entrega un reporte del formato especificado.
     *
     * @param string $format 'csv', 'xlsx', o 'pdf'.
     * @param Generator $data Fuente de datos eficiente.
     * @param string $filename Nombre del archivo de salida.
     * @return StreamedResponse
     */
    public function generateAndStream(string $format, Generator $data, string $filename): StreamedResponse
    {
        // 1. Crear el Producto (solicitar el formato deseado)
        $report = $this->getReportProduct($format);

        // 2. Cargar Datos Eficientemente (alimentar con el Generator)
        $report->setData($data);

        // 3. Entregar el Reporte (forzar la descarga)
        return $report->streamOutput($filename);
    }

    private function getReportProduct(string $format): IReportProduct
    {
        return match (strtolower($format)) {
            'csv' => $this->factory->createCsvReport(),
            'xlsx' => $this->factory->createXlsxReport(),
            'pdf' => $this->factory->createPdfReport(),
            default => throw new InvalidArgumentException("Formato de reporte no soportado: " . $format),
        };
    }
}
