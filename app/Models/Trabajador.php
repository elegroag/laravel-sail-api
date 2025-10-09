<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'nombres',
        'apellidos',
        'rut',
        'email',
        'telefono',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'cargo',
        'salario',
        'fecha_ingreso',
        'fecha_salida',
        'estado',
        'empresa_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
        'fecha_salida' => 'date',
        'salario' => 'decimal:2',
    ];

    // Relación muchos a uno con empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Relación uno a muchos con núcleo familiar
    public function nucleosFamiliares()
    {
        return $this->hasMany(NucleoFamiliar::class);
    }
}
