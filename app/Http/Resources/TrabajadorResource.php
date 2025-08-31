<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrabajadorResource extends JsonResource
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
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'nombre_completo' => $this->nombres . ' ' . $this->apellidos,
            'rut' => $this->rut,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'edad' => $this->fecha_nacimiento?->age,
            'genero' => $this->genero,
            'direccion' => $this->direccion,
            'cargo' => $this->cargo,
            'salario' => $this->salario,
            'salario_formateado' => '$' . number_format($this->salario, 0, ',', '.'),
            'fecha_ingreso' => $this->fecha_ingreso?->format('Y-m-d'),
            'fecha_salida' => $this->fecha_salida?->format('Y-m-d'),
            'antiguedad_dias' => $this->fecha_ingreso?->diffInDays(now()),
            'estado' => $this->estado,
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relaciones
            'empresa' => new EmpresaResource($this->whenLoaded('empresa')),
            'nucleos_familiares' => NucleoFamiliarResource::collection($this->whenLoaded('nucleosFamiliares')),

            // Estadísticas calculadas
            'total_familiares' => $this->when(
                $this->relationLoaded('nucleosFamiliares'),
                fn() => $this->nucleosFamiliares->count()
            ),
            'dependientes_economicos' => $this->when(
                $this->relationLoaded('nucleosFamiliares'),
                fn() => $this->nucleosFamiliares->where('dependiente_economico', true)->count()
            ),
        ];
    }
}
