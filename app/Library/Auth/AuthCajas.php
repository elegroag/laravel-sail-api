<?php

namespace App\Library\Auth;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Models\Gener02;
use App\Services\Api\ApiSubsidio;
use App\Services\CajaServices\UsuarioServices;

class AuthCajas
{

    private $usuario;

    public function autenticar($user, $clave): ?bool
    {
        SessionCookies::destroyIdentity();

        $dataUser = $this->getUsuario($user);
        $usuarioServices = new UsuarioServices();
        $usuarioServices->actualizaUsuario((object) $dataUser);

        $this->buscarUsuario($user);
        if (! $this->usuario) {
            throw new AuthException('El usuario es requerido para la autenticación. 2', 2);
        }
        if (! $clave) {
            throw new AuthException('La clave es requerida para la autenticación. 3', 3);
        }
        $criptada = $this->usuario->getCriptada();
        if (! clave_verify($clave, $criptada)) {
            throw new AuthException('La clave no es correcta para continuar con la autenticación. 6', 6);
        }
        return $this->crearSession();
    }

    public function crearSession(): ?bool
    {
        if (! SessionCookies::authenticate(
            new SessionCajas(),
            [
                'tipfun' => $this->usuario->getTipfun(),
                'usuario' => $this->usuario->getUsuario(),
                'estado' => $this->usuario->getEstado(),
                'cedtra' => $this->usuario->getCedtra(),
            ]
        )) {
            throw new AuthException('Error acceso incorrecto. No se logra completar la autenticación', 504);
        } else {
            return true;
        }
    }

    public function cargarIntentos($usuario)
    {
        $user = Gener02::where('estado', 'A')->where('usuario', $usuario)->first();
        if ($user) {
            $intentos = $user->intentos + 1;
            if ($intentos >= 3) {
                Gener02::where('usuario', $usuario)->update([
                    'estado' => 'B',
                    'intentos' => $intentos,
                ]);
                throw new AuthException('El usuario se ha bloqueado, por fallar en la autenticación con más de 3 intentos.', 7);
            } else {
                Gener02::where('usuario', $usuario)->update([
                    'intentos' => $intentos,
                ]);
            }
        }
    }

    public function buscarUsuario($user)
    {
        $this->usuario = Gener02::where('usuario', $user)->whereIn('estado', ['A', 'B'])->first();
        if (! $this->usuario) {
            throw new AuthException('El usuario no es correcto para continuar con la autenticación. 4', 4);
        }
        if ($this->usuario->getEstado() == 'B') {
            throw new AuthException('El usuario se encuentra bloqueado, por fallar en la autenticación con más de 3 intentos.' .
                ' Para poder desbloquear su cuenta puede recuperar la cuenta de usuario o solicitar el desbloqueo de su cuenta, ' .
                'al aréa de sistemas, soporte_sistemas@comfaca.com.', 5);
        }

        return $this->usuario;
    }

    /**
     * Obtener el usuario actual después de la autenticación
     */
    public function getUsuario(?string $usuario = null)
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'Usuarios',
                'metodo' => 'trae_usuario',
                'params' => $usuario ?? $this->usuario->getUsuario(),
            ]
        );

        if ($procesadorComando->isJson() == false) {
            throw new DebugException('Error al buscar la beneficiario en Sisuweb', 501);
        }

        $out = $procesadorComando->toArray();
        $userSisu = $out['data'];

        return $userSisu;
    }
}
