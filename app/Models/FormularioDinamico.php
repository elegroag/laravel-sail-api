<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormularioDinamico extends Model
{
    protected $table = 'formularios_dinamicos';

    protected $fillable = [
        'name',
        'title',
        'description',
        'module',
        'endpoint',
        'method',
        'is_active',
        'layout_config',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relación con componentes dinámicos, tiene muchos componentes dinámicos
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(ComponenteDinamico::class, 'formulario_id');
    }

    /**
     * Scope para formularios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para buscar por módulo
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }
}
