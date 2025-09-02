<?php
require_service('FactoryReportes/ExcelReportGenerator');
require_service('FactoryReportes/ReportFactory');

class ExcelReportFactory implements ReportFactory
{
    public function createReportGenerator()
    {
        return new ExcelReportGenerator();
    }
}
