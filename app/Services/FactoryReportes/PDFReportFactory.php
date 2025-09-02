<?php

namespace App\Services\FactoryReportes;

class PDFReportFactory implements ReportFactory
{
    public function createReportGenerator()
    {
        return new PDFReportGenerator();
    }
}
