<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio63 extends ModelBase
{
    protected $table = 'mercurio63';
    protected $primaryKey = 'numero';
    
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'numero',
        'tipo',
        'documento',
        'coddoc',
        'detalle',
        'tipmov',
        'movimiento',
        'valor',
        'hora',
        'fecsis',
        'estado',
    ];
}
