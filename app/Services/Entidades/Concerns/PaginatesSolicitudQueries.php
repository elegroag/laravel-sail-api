<?php

namespace App\Services\Entidades\Concerns;

use Illuminate\Support\Facades\DB;

trait PaginatesSolicitudQueries
{
    /**
     * @return array<int, array<string, mixed>>
     */
    protected function selectAssoc(string $sql): array
    {
        return json_decode(json_encode(DB::select($sql)), true) ?? [];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function selectOneAssoc(string $sql): ?array
    {
        $row = DB::selectOne($sql);
        if ($row === null) {
            return null;
        }

        return json_decode(json_encode($row), true);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int}
     */
    protected function paginateRawQuery(string $selectSql, int $page, int $perPage): array
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
        $total = (int) (DB::selectOne($countSql)->c ?? 0);

        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;
        $pagedSql = $baseSql.($orderSql !== '' ? ' '.$orderSql : '')." LIMIT {$perPage} OFFSET {$offset}";
        $items = $this->selectAssoc($pagedSql);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }
}
