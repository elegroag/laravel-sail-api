<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio66 extends Model
{
    protected $table = 'mercurio66';
    public $timestamps = false;
    public $incrementing = true; 
    protected $keyType = 'int';
    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'codsed',
        'detalle',
        'valor',
        'fecsis',
        'hora',
        'estado',
        'fecest',
        'tipo',
        'documento',
        'coddoc',
    ];
}
