<?php

namespace App\Services\ReportGenerator\Products;

use App\Services\ReportGenerator\Contracts\IReportProduct;
use Generator;

class OptimizedXlsxProduct implements IReportProduct
{
    public function setData(Generator $data): void
    {
        throw new \BadMethodCallException('OptimizedXlsxProduct is not yet implemented.');
    }

    public function generateContent(): string
    {
        throw new \BadMethodCallException('OptimizedXlsxProduct is not yet implemented.');
    }

    public function streamOutput(string $filename): void
    {
        throw new \BadMethodCallException('OptimizedXlsxProduct is not yet implemented.');
    }
}
