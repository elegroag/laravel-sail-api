<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xml4b091 extends Model
{
    protected $table = 'xml4b091';
    protected $primaryKey = 'codpai';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codpai',
        'detpai',
    ];
}
