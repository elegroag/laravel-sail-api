<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Empresa extends ModelBase
{

    protected $fillable = [
        'nombre',
        'rut',
        'direccion',
        'telefono',
        'email',
        'sector_economico',
        'numero_empleados',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'numero_empleados' => 'integer',
    ];

    // Relación uno a muchos con trabajadores
    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
}
