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

    protected $keyType = 'int';

    protected $fillable = [
        'menu_tipo',
        'tipfun',
        'can_view',
    ];
}
