<?php

namespace App\Services\Autentications;

use App\Exceptions\DebugException;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Request;

class AutenticaService
{
    public function execute(Request $request): array|null
    {
        // Sanitización ligera
        $tipo = $request->getParam("tipo");
        $documento = $request->getParam("documento");
        $coddoc = $request->getParam("coddoc");
        $clave = $request->getParam("clave");
        $res = False;

        // Validación básica de requeridos
        if ($tipo === '' || $documento === '' || $coddoc === '') {
            throw new DebugException("Error de acceso, los parámetros tipo, documento y coddoc son requeridos.", 422);
        }

        switch ($tipo) {
            case 'E':
                $autentica = new AutenticaEmpresa();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            case 'T':
                $autentica = new AutenticaTrabajador();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            case 'I':
                $autentica = new AutenticaIndependiente();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            case 'O':
                $autentica = new AutenticaPensionado();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            case 'F':
                $autentica = new AutenticaFacultativo();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            case 'P':
                $autentica = new AutenticaParticular();
                $res = $autentica->comprobarSISU($documento, $coddoc);
                break;
            default:
                throw new DebugException("Error de acceso, el tipo ingreso es requerido.", 501);
                break;
        }

        if ($res == False) {
            return [
                false,
                $autentica->getMessage(),
            ];
        }

        $mercurio07 = Mercurio07::where("tipo", $tipo)
            ->where("documento", $documento)
            ->where("coddoc", $coddoc)
            ->first();

        if ($mercurio07 == False) $mercurio07 = $autentica->getAfiliado();

        if ($mercurio07 == False) {
            throw new DebugException("Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.", 501);
        }

        // Validar estado activo si existe campo estado
        if (method_exists($mercurio07, 'getEstado')) {
            $estado = $mercurio07->getEstado();
            if (!empty($estado) && $estado !== 'A') {
                throw new DebugException("La cuenta no se encuentra activa para iniciar sesión.", 403);
            }
        }

        if ($clave === 'xxxx') {

            if ($tipo == 'N' || $tipo == 'P') {
                throw new DebugException("Alerta. El usuario ya posee un registro en plataforma y requiere de ingresar con la clave valida.", 501);
            } else {
                //create validation mediante token
                $codigoVerify = genera_code();
                $autentica->verificaPin($mercurio07, $codigoVerify);

                $authJwt = new AuthJwt();
                $token = $authJwt->SimpleToken();

                $user19 = Mercurio19::where(["documento" => $documento, "coddoc" => $coddoc, "tipo" => $tipo])->first();
                $inicio  = date('Y-m-d H:i:s');
                if ($user19) {
                    $momento = new \DateTime($user19->getInicio());
                    // Obtener el momento actual
                    $ahora = new \DateTime("now");
                    // Calcular la diferencia
                    $diferencia = $momento->diff($ahora);
                    // Convertir la diferencia a minutos
                    $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
                    if ($diferenciaEnMinutos >= 5) {
                        $intentos = 0;
                    } else {
                        $intentos = $user19->getIntentos() ? $user19->getIntentos() + 1 : 0;
                    }

                    Mercurio19::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('tipo', $tipo)
                        ->update([
                            'intentos' => (int) $intentos,
                            'inicio'   => $inicio,
                            'codver'   => (string) $codigoVerify,
                            'token'    => (string) $token,
                        ]);
                } else {
                    $user19 = new Mercurio19();
                    $user19->setTipo($tipo);
                    $user19->setCoddoc($coddoc);
                    $user19->setDocumento($documento);
                    $user19->setIntentos(0);
                    $user19->setInicio($inicio);
                    $user19->setCodver($codigoVerify);
                    $user19->setToken($token);
                    $user19->setCodigo(1);
                    if (!$user19->save()) {
                        $msj = '';
                        foreach ($user19->getMessages() as $message)  $msj .= ' ' . $message->getMessage();
                        throw new DebugException("Error al guardar Token Access, {$msj}", 501);
                    }
                }

                $this->autoFirma($documento, $coddoc, $clave);

                return [
                    false,
                    "Alerta. El usuario ya posee un registro en plataforma y requiere de ingresar con PIN de validación.",
                ];
            }
        }

        $storedHash = $mercurio07->getClave();
        if (!clave_verify($clave, $storedHash)) {
            throw new DebugException("Error el valor de la clave no es válido para ingresar a la plataforma.", 503);
        }

        $estadoAfiliado = $autentica->getEstadoAfiliado();

        $auth = new SessionCookies(
            "model: mercurio07",
            "tipo: {$tipo}",
            "coddoc: {$coddoc}",
            "documento: {$documento}",
            "estado_afiliado: {$estadoAfiliado}",
            "estado: A",
        );

        if (!$auth->authenticate()) {
            throw new DebugException("Error acceso incorrecto. No se logra completar la autenticación", 504);
        }
        $this->autoFirma($documento, $coddoc, $clave);

        return [
            true,
            "La autenticación se ha completado con éxito."
        ];
    }


    function autoFirma($documento, $coddoc, $clave)
    {
        $gestionFirmas = new GestionFirmaNoImage(
            array(
                "documento" => $documento,
                "coddoc" => $coddoc
            )
        );
        if ($gestionFirmas->hasFirma() == False) {
            $gestionFirmas->guardarFirma();
            $gestionFirmas->generarClaves($clave);
        } else {
            $firma = $gestionFirmas->getFirma();
            if (is_null($firma->getKeypublic()) || is_null($firma->getKeyprivate())) {
                $gestionFirmas->guardarFirma();
                $gestionFirmas->generarClaves($clave);
            }
        }
    }
}
