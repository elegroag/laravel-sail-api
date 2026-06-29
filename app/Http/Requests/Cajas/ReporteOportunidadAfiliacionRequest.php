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
            'tipafis' => ['nullable', 'array'],
            'tipafis.*' => ['integer', 'in:1,2,3,4,9,10,11'],
            'estado' => ['nullable'],
            'nit' => ['nullable', 'string', 'max:20'],
            'cedtra' => ['nullable', 'string', 'max:20'],
            'solo_vencidos' => ['nullable', 'boolean'],
            'solo_pendientes' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tipafis') && is_string($this->input('tipafis'))) {
            $this->merge([
                'tipafis' => array_values(array_filter(array_map('intval', explode(',', $this->input('tipafis'))))),
            ]);
        }

        $this->merge([
            'solo_vencidos' => filter_var($this->input('solo_vencidos', false), FILTER_VALIDATE_BOOLEAN),
            'solo_pendientes' => filter_var($this->input('solo_pendientes', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function filtros(): array
    {
        return $this->validated();
    }
}
