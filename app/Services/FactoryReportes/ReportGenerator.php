<?php

namespace App\Services\FactoryReportes;

interface ReportGenerator
{
    public function generateReport($title, $file, $columns);
}
