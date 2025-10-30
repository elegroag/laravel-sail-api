<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Radicado extends Model
{
    protected $table = 'radicado';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'vigencia',
        'tipo',
        'numero',
        'radicado',
    ];
}
