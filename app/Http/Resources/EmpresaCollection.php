<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmpresaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_empresas' => $this->collection->count(),
                'empresas_activas' => $this->collection->where('estado', 'activa')->count(),
                'empresas_inactivas' => $this->collection->where('estado', 'inactiva')->count(),
                'total_empleados' => $this->collection->sum('numero_empleados'),
                'sectores_economicos' => $this->collection->pluck('sector_economico')->filter()->unique()->values(),
            ],
            'links' => [
                'self' => $request->url(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Empresas obtenidas exitosamente',
            'timestamp' => now()->toISOString(),
        ];
    }
}
