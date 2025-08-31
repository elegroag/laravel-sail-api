<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NucleoFamiliarCollection extends ResourceCollection
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
                'total_familiares' => $this->collection->count(),
                'dependientes_economicos' => $this->collection->where('dependiente_economico', true)->count(),
                'independientes_economicos' => $this->collection->where('dependiente_economico', false)->count(),
                'distribucion_parentesco' => [
                    'conyuge' => $this->collection->where('parentesco', 'conyuge')->count(),
                    'hijo' => $this->collection->where('parentesco', 'hijo')->count(),
                    'hija' => $this->collection->where('parentesco', 'hija')->count(),
                    'padre' => $this->collection->where('parentesco', 'padre')->count(),
                    'madre' => $this->collection->where('parentesco', 'madre')->count(),
                    'hermano' => $this->collection->where('parentesco', 'hermano')->count(),
                    'hermana' => $this->collection->where('parentesco', 'hermana')->count(),
                    'otro' => $this->collection->where('parentesco', 'otro')->count(),
                ],
                'distribucion_genero' => [
                    'masculino' => $this->collection->where('genero', 'masculino')->count(),
                    'femenino' => $this->collection->where('genero', 'femenino')->count(),
                    'otro' => $this->collection->where('genero', 'otro')->count(),
                ],
                'edad_promedio' => $this->collection->avg(function ($item) {
                    return $item->fecha_nacimiento ? $item->fecha_nacimiento->age : null;
                }),
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
            'message' => 'Núcleos familiares obtenidos exitosamente',
            'timestamp' => now()->toISOString(),
        ];
    }
}
