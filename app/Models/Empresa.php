<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

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
