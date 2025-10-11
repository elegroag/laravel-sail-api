<?php

namespace App\Services\ReportGenerator\Contracts;

use App\Services\ReportGenerator\Contracts\IReportProduct;

interface IReportFactory
{
    public function createCsvReport(): IReportProduct;
    public function createXlsxReport(): IReportProduct;
    public function createPdfReport(): IReportProduct;
}
