<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio68 extends Model
{
    protected $table = 'mercurio68';
    public $incrementing = true;
    public $timestamps = false;
    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'tipo',
        'documento',
        'coddoc',
        'tipben',
        'docben',
        'codben',
        'valor',
        'email',
        'fecsis',
        'hora',
    ];
}
