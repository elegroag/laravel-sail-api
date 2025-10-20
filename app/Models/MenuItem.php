<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class MenuItem extends ModelBase
{
    protected $table = 'menu_items';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'title',
        'default_url',
        'icon',
        'color',
        'nota',
        'parent_id',
        'codapl',
        'controller',
        'action'
    ];
}
