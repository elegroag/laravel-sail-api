<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio69 extends Model
{
    protected $table = 'mercurio69';
    public $incrementing = true;
    public $timestamps = false;
    protected $primaryKey = 'numero';

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
