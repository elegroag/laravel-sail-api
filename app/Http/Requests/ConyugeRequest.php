<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConyugeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cedtra' => 'required|numeric|min:5|max:18',
            'cedcon' => 'required|numeric',
            'priape' => 'required|string|max:255',
            'segape' => 'nullable|string|max:255',
            'prinom' => 'required|string|max:255',
            'segnom' => 'nullable|string|max:255',
            'telefono' => 'required|numeric|digits:10',
            'celular' => 'required|numeric|digits:10',
            'email' => 'required|email|max:255',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio',
            'numeric' => 'El campo :attribute debe ser numérico',
            'email' => 'El campo :attribute debe ser un email válido',
            'digits' => 'El campo :attribute debe tener :digits dígitos',
        ];
    }
}
