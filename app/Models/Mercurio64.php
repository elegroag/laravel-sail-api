<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mercurio64 extends Model
{

    protected $table = 'mercurio64';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'numero';

    protected $fillable = [
        'numero',
        'tipo',
        'documento',
        'coddoc',
        'tipmov',
        'pergir',
        'online',
        'transferencia',
        'valor',
        'fecsis',
        'hora',
        'estado',
    ];

}
