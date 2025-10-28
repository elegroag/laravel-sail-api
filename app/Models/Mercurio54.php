<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio54 extends ModelBase { 

    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio54';
    
    protected $primaryKey = [
        'tipo',
        'coddoc',
        'documento',
    ];
    public $incrementing = true;
    public $timestamps = false;   
    
    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'token',
        'tokencel',
        'tiptra',
        'codtra',
        'doctra',
    ];

    protected function rules()
    {
        return [
            'documento' => 'required|numeric|min:5',
            'coddoc' => 'required|numeric|min:1',
            'tipo' => 'required|string|min:0',
            '_id' => [
                'required|string',
                Rule::unique('mercurio54')->where(function ($query) {
                    return $query->where('documento', $this->documento)
                        ->where('coddoc', $this->coddoc)
                        ->where('tipo', $this->tipo);
                }),
            ],
        ];
    }



}
