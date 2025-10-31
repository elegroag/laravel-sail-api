<?php

namespace App\Services\Reports;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvReportStrategy implements ReportFormatStrategy
{
    public function __construct(
        private string $delimiter = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
        private bool $withBom = true,
    ) {}

    public function stream(Builder $query, array $columns, int $chunkSize = 1000): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ];

        return new StreamedResponse(function () use ($query, $columns, $chunkSize) {
            $out = fopen('php://output', 'w');
            if ($this->withBom) {
                // UTF-8 BOM for Excel compatibility
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            }

            // headers
            fputcsv($out, array_keys($columns), $this->delimiter, $this->enclosure, $this->escape);

            $query->chunk($chunkSize, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    $payload = [];
                    foreach ($columns as $header => $accessor) {
                        if (is_callable($accessor)) {
                            $payload[] = (string) $accessor($row);
                        } else {
                            $payload[] = (string) data_get($row, $accessor);
                        }
                    }
                    fputcsv($out, $payload, $this->delimiter, $this->enclosure, $this->escape);
                }
            });

            fclose($out);
        }, 200, $headers);
    }
}
