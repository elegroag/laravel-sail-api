<?php

namespace App\Services\Reports;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\Builder;

interface ReportFormatStrategy
{
    /**
     * Build a StreamedResponse for the given query and column spec.
     *
     * @param Builder $query Eloquent query already filtered/sorted
     * @param array $columns Associative array: header => accessor
     *                       accessor: string field name or callable($row): mixed
     * @param int $chunkSize Number of records per chunk
     */
    public function stream(Builder $query, array $columns, int $chunkSize = 1000): StreamedResponse;
}
