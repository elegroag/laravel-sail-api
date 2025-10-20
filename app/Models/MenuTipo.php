<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class MenuTipo extends ModelBase
{
    protected $table = 'menu_tipos';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'id',
        'menu_item',
        'is_visible',
        'tipo',
        'position'
    ];
}
