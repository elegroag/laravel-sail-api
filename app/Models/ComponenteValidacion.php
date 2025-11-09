<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponenteValidacion extends Model
{
    protected $table = 'componentes_validaciones';

    protected $fillable = [
        'componente_id',
        'pattern',
        'default_value',
        'max_length',
        'min_length',
        'numeric_range',
        'field_size',
        'detail_info',
        'is_required',
        'custom_rules',
        'error_messages',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'custom_rules' => 'array',
        'error_messages' => 'array',
    ];

    public function componente(): BelongsTo
    {
        return $this->belongsTo(ComponenteDinamico::class, 'componente_id');
    }
}
