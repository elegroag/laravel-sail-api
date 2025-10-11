<?php

namespace App\Services\ReportGenerator\Factories;

use App\Services\ReportGenerator\Contracts\IReportFactory;
use App\Services\ReportGenerator\Contracts\IReportProduct;
use App\Services\ReportGenerator\Products\CsvStreamingProduct;
use App\Services\ReportGenerator\Products\DompdfProduct;
use App\Services\ReportGenerator\Products\OptimizedXlsxProduct;

class OptimizedReportFactory implements IReportFactory
{
    public function createCsvReport(): IReportProduct
    {
        // Usa un producto optimizado para streaming y bajo consumo de memoria.
        return new CsvStreamingProduct();
    }

    public function createXlsxReport(): IReportProduct
    {
        // Utiliza Cell Caching (PSR-16) o Box/Spout para eficiencia.
        return new OptimizedXlsxProduct();
    }

    public function createPdfReport(): IReportProduct
    {
        // Usa Snappy o Dompdf para la generación de PDFs.
        return new DompdfProduct();
    }
}
