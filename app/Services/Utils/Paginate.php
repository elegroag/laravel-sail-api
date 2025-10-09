<?php

namespace App\Services\Utils;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator as SimplePaginator;
use Illuminate\Support\Collection;

class Paginate
{
    public static function execute($collectModel, $pageNumber = null, $cantidadPages = 10)
    {
        // Normaliza página
        if ($pageNumber === null || ! is_numeric($pageNumber) || (int) $pageNumber < 1) {
            $pageNumber = 1;
        } else {
            $pageNumber = (int) $pageNumber;
        }

        // Helper de salida compatible
        $toStd = function ($items, int $total, int $perPage, int $currentPage) {
            $page = new \stdClass;
            $page->items = $items;
            $page->num_rows = $total;
            $page->first = 1;
            $page->current = $currentPage;
            $page->total_pages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
            if ($page->total_pages < 1) {
                $page->total_pages = 1;
            }
            $page->before = ($currentPage > 1) ? ($currentPage - 1) : 1;
            $page->last = $page->total_pages;
            $page->next = ($currentPage < $page->last) ? ($currentPage + 1) : $currentPage;

            return $page;
        };

        // 1) Si es un Builder de Eloquent/Query, usar paginate nativo
        if ($collectModel instanceof EloquentBuilder || $collectModel instanceof QueryBuilder) {
            $paginator = $collectModel->paginate($cantidadPages, ['*'], 'page', $pageNumber);

            return self::fromLengthAwarePaginator($paginator);
        }

        // 2) Si ya es un LengthAwarePaginator
        if ($collectModel instanceof LengthAwarePaginatorContract) {
            return self::fromLengthAwarePaginator($collectModel);
        }

        // 3) Si es un SimplePaginator
        if ($collectModel instanceof SimplePaginator) {
            $items = $collectModel->items();
            // SimplePaginator no conoce el total real; aproximamos con páginas
            $perPage = $collectModel->perPage();
            // Se asume que no hay total; calculamos con la cantidad actual para mantener compatibilidad
            $total = count($items) + ($perPage * ($pageNumber - 1));

            return $toStd($items, $total, $perPage, $pageNumber);
        }

        // 4) Si es una Collection
        if ($collectModel instanceof Collection) {
            $total = $collectModel->count();
            $items = $collectModel->forPage($pageNumber, $cantidadPages)->values();

            return $toStd($items, $total, $cantidadPages, $pageNumber);
        }

        // 5) Si es un array plano
        if (is_array($collectModel)) {
            $total = count($collectModel);
            $start = $cantidadPages * ($pageNumber - 1);
            $items = array_slice($collectModel, $start, $cantidadPages);

            return $toStd($items, $total, $cantidadPages, $pageNumber);
        }

        // 6) Último recurso: tratar como colección vacía
        return $toStd([], 0, $cantidadPages, $pageNumber);
    }

    /**
     * Convierte un LengthAwarePaginator de Laravel al formato esperado por los controladores existentes
     */
    private static function fromLengthAwarePaginator(LengthAwarePaginatorContract $paginator)
    {
        $page = new \stdClass;
        // Items puede ser Collection; mantenemos tal cual para compatibilidad
        $page->items = $paginator->items();
        $page->num_rows = (int) $paginator->total();
        $page->first = 1;
        $page->current = (int) $paginator->currentPage();
        $page->total_pages = (int) $paginator->lastPage();
        if ($page->total_pages < 1) {
            $page->total_pages = 1;
        }
        $page->before = ($page->current > 1) ? ($page->current - 1) : 1;
        $page->last = $page->total_pages;
        $page->next = ($page->current < $page->last) ? ($page->current + 1) : $page->current;

        return $page;
    }
}
