<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio61 extends Model
{
    protected $table = 'mercurio61';
    protected $fillable = [
        'numero',
        'item',
        'tipo',
        'documento',
        'cantidad',
        'valor',
    ];
}
