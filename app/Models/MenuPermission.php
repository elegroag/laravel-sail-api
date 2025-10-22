<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

/**
 * Modelo MenuPermission
 * - Sin timestamps
 * - PK autoincremental `id`
 */
class MenuPermission extends ModelBase
{
    protected $table = 'menu_permissions';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'menu_item',
        'tipfun',
        'can_view',
        'opciones'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item', 'id');
    }

    public function tipfun()
    {
        return $this->belongsTo(Gener21::class, 'tipfun', 'tipfun');
    }
}
