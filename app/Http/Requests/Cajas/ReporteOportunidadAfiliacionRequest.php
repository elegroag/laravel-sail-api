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
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('tipafis')) {
            return;
        }

        $raw = $this->input('tipafis');

        if ($raw === null || $raw === '' || $raw === []) {
            $this->merge(['tipafis' => null]);

            return;
        }

        if (is_array($raw)) {
            $values = array_values(array_filter(array_map('intval', $raw), static fn (int $v) => $v > 0));
            $this->merge(['tipafis' => $values === [] ? null : $values]);

            return;
        }

        $values = array_values(array_filter(
            array_map('intval', explode(',', (string) $raw)),
            static fn (int $v) => $v > 0
        ));

        $this->merge(['tipafis' => $values === [] ? null : $values]);
    }

    /**
     * @return array<string, mixed>
     */
    public function filtros(): array
    {
        return $this->validated();
    }
}
