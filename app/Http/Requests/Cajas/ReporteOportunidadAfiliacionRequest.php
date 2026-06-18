<?php

namespace App\Http\Requests\Cajas;

use Illuminate\Foundation\Http\FormRequest;

class ReporteOportunidadAfiliacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecini'],
            'campo_fecha' => ['nullable', 'in:fecsol,sat_fecapr,fecapr'],
            'tipafis' => ['nullable', 'array'],
            'tipafis.*' => ['integer', 'in:1,2,3,4,9,10,11'],
            'estado' => ['nullable'],
            'nit' => ['nullable', 'string', 'max:20'],
            'cedtra' => ['nullable', 'string', 'max:20'],
            'modalidad' => ['nullable', 'in:aportante,trabajador'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tipafis') && is_string($this->input('tipafis'))) {
            $this->merge([
                'tipafis' => array_values(array_filter(array_map('intval', explode(',', $this->input('tipafis'))))),
            ]);
        }

        if (! $this->filled('campo_fecha')) {
            $this->merge(['campo_fecha' => 'fecsol']);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function filtros(): array
    {
        return $this->validated();
    }
}
