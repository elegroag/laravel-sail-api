<?php

namespace App\Http\Controllers\Mercurio\Concerns;

use Illuminate\Http\Request;

trait RendersSolicitudesGrid
{
    protected function renderSolicitudesGrid(
        Request $request,
        ?string $estado,
        object $service,
        string $view,
        string $collectionKey,
    ) {
        try {
            if (! $request->has('page') && ! $request->has('per_page')) {
                $items = $service->findAllByEstado($estado);
                $html = view($view, [
                    'path' => base_path(),
                    $collectionKey => $items,
                ])->render();

                $this->setResponse('view');

                return $this->renderText($html);
            }

            $page = max(1, (int) $request->input('page', 1));
            $perPage = max(1, min(100, (int) $request->input('per_page', 10)));

            $paginated = $service->findByEstadoPaginated($estado, $page, $perPage);
            $items = $paginated['items'];
            $total = (int) $paginated['total'];
            $currentPage = (int) $paginated['page'];
            $perPage = (int) $paginated['per_page'];
            $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;
            $from = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
            $to = $total > 0 ? min($from + count($items) - 1, $total) : 0;

            $consulta = view($view, [
                'path' => base_path(),
                $collectionKey => $items,
                'paginated' => true,
                'total' => $total,
            ])->render();

            return $this->renderObject([
                'consulta' => $consulta,
                'meta' => [
                    'current' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'from' => $from,
                    'to' => $to,
                ],
            ]);
        } catch (\Throwable $e) {
            if ($request->has('page') || $request->has('per_page')) {
                return $this->renderObject($this->captureException($e));
            }

            return $this->renderText($this->captureException($e));
        }
    }
}
