<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

/**
 * Snapshot de solicitud borrada (poblado por trigger AFTER DELETE).
 */
abstract class AuditoriaSolicitudBase extends ModelBase
{
    public $timestamps = false;

    public $incrementing = true;

    protected $primaryKey = 'audit_id';

    protected $guarded = [];
}
