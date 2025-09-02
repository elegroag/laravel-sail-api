<?php
require_service('FactoryReportes/ReportFactory');
require_service('FactoryReportes/PDFReportGenerator');

class PDFReportFactory implements ReportFactory
{
    public function createReportGenerator()
    {
        return new PDFReportGenerator();
    }
}
