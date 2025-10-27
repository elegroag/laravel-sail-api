<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio71 extends Model
{
    protected $table = 'mercurio71';

    protected $fillable = [
        'numero',
        'tipo',
        'documento',
        'coddoc',
        'codser',
        'puntos',
        'fecsis',
        'hora',
    ];
}
