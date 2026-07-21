<?php

namespace App\Http\Requests\Cajas;

use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InformeSolicitudRequest extends FormRequest
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
            'ruuid' => ['required', 'string', 'min:8', 'max:40'],
            'tipopc' => ['required', Rule::in(AuditoriaSolicitudResolver::tipopcsInforme())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ruuid.required' => 'El RUUID de la solicitud es obligatorio.',
            'tipopc.required' => 'El tipo de afiliación es obligatorio.',
            'tipopc.in' => 'El tipo de afiliación no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tipopc')) {
            $this->merge(['tipopc' => (string) $this->input('tipopc')]);
        }

        if ($this->has('ruuid')) {
            $this->merge(['ruuid' => trim((string) $this->input('ruuid'))]);
        }
    }
}
