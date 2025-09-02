<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NucleoFamiliarResource extends JsonResource
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
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'edad' => $this->fecha_nacimiento?->age,
            'genero' => $this->genero,
            'parentesco' => $this->parentesco,
            'parentesco_formateado' => ucfirst($this->parentesco),
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'estado_civil' => $this->estado_civil,
            'estado_civil_formateado' => $this->estado_civil ? ucwords(str_replace('_', ' ', $this->estado_civil)) : null,
            'ocupacion' => $this->ocupacion,
            'dependiente_economico' => $this->dependiente_economico,
            'es_dependiente' => $this->dependiente_economico ? 'Sí' : 'No',
            'fecha_creacion' => $this->created_at?->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relaciones
            'trabajador' => new TrabajadorResource($this->whenLoaded('trabajador')),

            // Información adicional del trabajador cuando está cargado
            'empresa_trabajador' => $this->when(
                $this->relationLoaded('trabajador') && $this->trabajador->relationLoaded('empresa'),
                fn() => [
                    'nombre_empresa' => $this->trabajador->empresa->nombre,
                    'cargo_trabajador' => $this->trabajador->cargo,
                ]
            ),
        ];
    }
}
