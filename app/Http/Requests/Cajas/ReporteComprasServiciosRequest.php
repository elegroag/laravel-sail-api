<?php

namespace App\Http\Requests\Cajas;

use App\Services\Ecommerce\EstadoPrecompra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReporteComprasServiciosRequest extends FormRequest
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
            'estado' => ['nullable', 'string', Rule::in(array_keys(EstadoPrecompra::DESCRIPCIONES))],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecini.required' => 'La fecha inicial es obligatoria.',
            'fecfin.required' => 'La fecha final es obligatoria.',
            'fecfin.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('estado') && $this->input('estado') === '') {
            $this->merge(['estado' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function filtros(): array
    {
        $data = $this->validated();
        $data['page'] = (int) ($data['page'] ?? 1);
        $data['per_page'] = (int) ($data['per_page'] ?? 25);

        return $data;
    }
}
