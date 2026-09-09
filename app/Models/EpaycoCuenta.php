<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpaycoCuenta extends Model
{
    protected $table = 'epayco_cuentas';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account',
        'env_mode',
        'public_key',
        'private_key',
        'p_key',
        'p_id_customer',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'private_key',
        'p_key',
    ];
}
