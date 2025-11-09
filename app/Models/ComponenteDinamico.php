<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComponenteDinamico extends Model
{
    protected $table = 'componentes_dinamicos';

    protected $fillable = [
        'name',
        'type',
        'label',
        'placeholder',
        'form_type',
        'group_id',
        'order',
        'default_value',
        'is_disabled',
        'is_readonly',
        'data_source',
        'css_classes',
        'help_text',
        'target',
        'event_config',
        'search_type',
        'date_max',
        'number_min',
        'number_max',
        'number_step',
    ];

    protected $casts = [
        'is_disabled' => 'boolean',
        'is_readonly' => 'boolean',
        'data_source' => 'array',
        'event_config' => 'array',
        'date_max' => 'date',
        'number_min' => 'decimal:2',
        'number_max' => 'decimal:2',
        'number_step' => 'decimal:2',
    ];

    public function validacion(): HasOne
    {
        return $this->hasOne(ComponenteValidacion::class, 'componente_id');
    }
}
