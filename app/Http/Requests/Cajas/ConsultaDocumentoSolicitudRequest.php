<?php

namespace App\Http\Requests\Cajas;

use Illuminate\Foundation\Http\FormRequest;

class ConsultaDocumentoSolicitudRequest extends FormRequest
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
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'documento.required' => 'El documento de identificación es obligatorio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('documento')) {
            $this->merge(['documento' => trim((string) $this->input('documento'))]);
        }
    }

    /**
     * @return array{documento: string}
     */
    public function filtros(): array
    {
        return $this->validated();
    }
}
