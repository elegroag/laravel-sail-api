<?php

namespace App\Http\Requests\Cajas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReporteSolicitudesEmpresaRequest extends FormRequest
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
            'documento' => ['required', 'string', 'max:20'],
            'coddoc' => ['required', 'string', 'max:2'],
            'fecini' => ['required', 'date_format:Y-m-d'],
            'fecfin' => ['required', 'date_format:Y-m-d', 'after_or_equal:fecini'],
            'tipopcs' => ['required', 'array', 'min:1'],
            'tipopcs.*' => ['integer', Rule::in([1, 3, 4])],
            'estado' => ['nullable', 'string', Rule::in(['P', 'T', 'A', 'D', 'X', 'C', 'I'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            'refresh' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'documento.required' => 'El documento de la empresa es obligatorio.',
            'coddoc.required' => 'El tipo de documento es obligatorio.',
            'fecini.required' => 'La fecha inicial es obligatoria.',
            'fecfin.required' => 'La fecha final es obligatoria.',
            'tipopcs.required' => 'Seleccione al menos un tipo de solicitud.',
            'tipopcs.min' => 'Seleccione al menos un tipo de solicitud.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('documento')) {
            $this->merge(['documento' => trim((string) $this->input('documento'))]);
        }

        if ($this->has('coddoc')) {
            $this->merge(['coddoc' => trim((string) $this->input('coddoc'))]);
        }

        if ($this->has('estado') && $this->input('estado') === '') {
            $this->merge(['estado' => null]);
        }

        if ($this->has('tipopcs') && ! is_array($this->input('tipopcs'))) {
            $raw = $this->input('tipopcs');
            $values = is_string($raw)
                ? array_filter(array_map('intval', explode(',', $raw)))
                : [(int) $raw];
            $this->merge(['tipopcs' => array_values($values)]);
        }

        if ($this->has('refresh')) {
            $this->merge(['refresh' => filter_var($this->input('refresh'), FILTER_VALIDATE_BOOLEAN)]);
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
        $data['refresh'] = (bool) ($data['refresh'] ?? false);

        return $data;
    }
}
