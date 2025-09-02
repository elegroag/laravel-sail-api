<?php

namespace App\Services\FactoryReportes;

class ExcelReportFactory implements ReportFactory
{
    public function createReportGenerator()
    {
        return new ExcelReportGenerator();
    }
}
