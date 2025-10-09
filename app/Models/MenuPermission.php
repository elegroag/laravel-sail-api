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
        'menu_item_id',
        'role_id',
        'can_view',
    ];

    // Getters/Setters simples (compatibilidad y claridad)
    public function getId()
    {
        return $this->id;
    }

    public function setMenuItemId($v)
    {
        $this->menu_item_id = $v;
    }

    public function getMenuItemId()
    {
        return $this->menu_item_id;
    }

    public function setRoleId($v)
    {
        $this->role_id = $v;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setCanView($v)
    {
        $this->can_view = $v;
    }

    public function getCanView()
    {
        return $this->can_view;
    }
}
