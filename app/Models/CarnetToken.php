<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Illuminate\Support\Str;

class CarnetToken extends ModelBase
{
    protected $table = 'carnet_tokens';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'token',
        'coddoc',
        'documento',
        'estado',
        'fecha_creacion',
        'ultima_verificacion',
        'verificaciones',
    ];

    public function getToken()
    {
        return $this->token;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Token vigente del afiliado; lo crea si no existe y lo regenera si fue revocado.
     */
    public static function obtenerParaAfiliado(string $coddoc, string $documento): self
    {
        $carnetToken = static::firstOrNew([
            'coddoc' => $coddoc,
            'documento' => $documento,
        ]);

        if (! $carnetToken->exists || $carnetToken->getEstado() !== 'A') {
            $carnetToken->fill([
                'token' => Str::random(24),
                'estado' => 'A',
                'fecha_creacion' => now(),
                'verificaciones' => 0,
                'ultima_verificacion' => null,
            ]);
            $carnetToken->save();
        }

        return $carnetToken;
    }

    public static function findActivo(string $token): ?self
    {
        return static::where('token', $token)
            ->where('estado', 'A')
            ->first();
    }

    public function registrarVerificacion(): void
    {
        $this->ultima_verificacion = now();
        $this->verificaciones = (int) $this->verificaciones + 1;
        $this->save();
    }
}
