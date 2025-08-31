<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'rut' => $this->rut,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'sector_economico' => $this->sector_economico,
            'numero_empleados' => $this->numero_empleados,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Incluir trabajadores solo cuando están cargados
            'trabajadores' => TrabajadorResource::collection($this->whenLoaded('trabajadores')),

            // Estadísticas calculadas
            'total_trabajadores' => $this->when(
                $this->relationLoaded('trabajadores'),
                fn() => $this->trabajadores->count()
            ),
            'trabajadores_activos' => $this->when(
                $this->relationLoaded('trabajadores'),
                fn() => $this->trabajadores->where('estado', 'activo')->count()
            ),
        ];
    }
}
