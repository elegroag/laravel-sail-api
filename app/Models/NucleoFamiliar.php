<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NucleoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'nucleos_familiares';

    protected $fillable = [
        'nombres',
        'apellidos',
        'rut',
        'fecha_nacimiento',
        'genero',
        'parentesco',
        'telefono',
        'email',
        'direccion',
        'estado_civil',
        'ocupacion',
        'dependiente_economico',
        'trabajador_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'dependiente_economico' => 'boolean',
    ];

    // Relación muchos a uno con trabajador
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
