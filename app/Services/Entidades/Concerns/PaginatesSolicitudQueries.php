<?php

namespace App\Services\Entidades\Concerns;

use App\Models\Adapter\DbBase;
use Illuminate\Support\Facades\DB;

trait PaginatesSolicitudQueries
{
    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int}
     */
    protected function paginateRawQuery(string $selectSql, int $page, int $perPage, bool $useDbBase = false): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $normalizedSelect = trim($selectSql);
        $normalizedSelect = rtrim($normalizedSelect, ';');

        if (preg_match('/\sORDER\s+BY\s/i', $normalizedSelect, $matches, PREG_OFFSET_CAPTURE)) {
            $orderPos = $matches[0][1];
            $baseSql = trim(substr($normalizedSelect, 0, $orderPos));
            $orderSql = trim(substr($normalizedSelect, $orderPos));
        } else {
            $baseSql = $normalizedSelect;
            $orderSql = '';
        }

        $countSql = 'SELECT COUNT(*) as c FROM ('.$baseSql.') as solicitudes_paginadas';

        if ($useDbBase && property_exists($this, 'db') && $this->db instanceof DbBase) {
            $countRow = $this->db->inQueryAssoc($countSql);
            $total = (int) ($countRow[0]['c'] ?? 0);
        } else {
            $total = (int) (DB::selectOne($countSql)->c ?? 0);
        }

        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $pagedSql = $baseSql.($orderSql !== '' ? ' '.$orderSql : '')." LIMIT {$perPage} OFFSET {$offset}";

        if ($useDbBase && property_exists($this, 'db') && $this->db instanceof DbBase) {
            $items = $this->db->inQueryAssoc($pagedSql);
        } else {
            $items = json_decode(json_encode(DB::select($pagedSql)), true);
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }
}
