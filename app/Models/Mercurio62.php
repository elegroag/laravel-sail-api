<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio62 extends Model
{
    protected $table = 'mercurio62';
    protected $fillable = [
        'tipo',
        'documento',
        'coddoc',
        'salgir',
        'salrec',
        'consumo',
        'puntos',
        'punuti',
    ];
}
