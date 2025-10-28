<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use App\Models\Adapter\ValidateWithRules;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio62 extends Model
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio62';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = [
        'tipo',
        'documento',
        'coddoc',
    ];

    protected $fillable = [
        'tipo',
        'documento',
        'coddoc',
        'salgir',
        'salrec',
        'consumo',
        'puntos',
        'punuti',
    ];

    protected function rules()
    {
        return [
            'tipo' => 'required|numeric|max:2',
            'documento' => 'required|numeric|min:5',
            'coddoc' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio62')->where(function ($query) {
                    return $query->where('tipo', $this->tipo)
                        ->where('documento', $this->documento)
                        ->where('coddoc', $this->coddoc);
                }),
            ],
        ];
    }
}
