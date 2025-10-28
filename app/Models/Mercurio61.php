<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Thiagoprz\CompositeKey\HasCompositeKey;
use Illuminate\Validation\Rule;

class Mercurio61 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio61';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = [
        'numero',
        'item',
    ];

    protected $fillable = [
        'numero',
        'item',
        'tipo',
        'documento',
        'cantidad',
        'valor',
    ];

    protected function rules()
    {
        return [
            'numero' => 'required|numeric|max:11',
            'item' => 'required|numeric|max:11',
            '_id' => [
                'required|string',
                Rule::unique('mercurio61')->where(function ($query) {
                    return $query->where('numero', $this->numero)
                        ->where('item', $this->item);
                }),
            ],
        ];
    }
}
