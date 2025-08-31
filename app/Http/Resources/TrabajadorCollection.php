<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrabajadorCollection extends ResourceCollection
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
                'total_trabajadores' => $this->collection->count(),
                'trabajadores_activos' => $this->collection->where('estado', 'activo')->count(),
                'trabajadores_inactivos' => $this->collection->where('estado', 'inactivo')->count(),
                'trabajadores_suspendidos' => $this->collection->where('estado', 'suspendido')->count(),
                'salario_promedio' => $this->collection->avg('salario'),
                'salario_total' => $this->collection->sum('salario'),
                'cargos_unicos' => $this->collection->pluck('cargo')->unique()->values(),
                'distribucion_genero' => [
                    'masculino' => $this->collection->where('genero', 'masculino')->count(),
                    'femenino' => $this->collection->where('genero', 'femenino')->count(),
                    'otro' => $this->collection->where('genero', 'otro')->count(),
                ],
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
            'message' => 'Trabajadores obtenidos exitosamente',
            'timestamp' => now()->toISOString(),
        ];
    }
}
