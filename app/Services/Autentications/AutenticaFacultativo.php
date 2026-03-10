<?php

namespace App\Services\Autentications;

use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Utils\CrearUsuario;

class AutenticaFacultativo extends AutenticaGeneral
{
    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'F';
        $this->tipoName = 'Facultativo';
    }

    public function comprobarSISU($documento, $coddoc)
    {
        /**
         * buscar empresa en sisu
         */
        $this->procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $documento,
                ],
            ]
        );
        $out = $this->procesadorComando->toArray();
        if (!is_array($out)) {
            $this->message = 'Se genero un error al buscar al afiliado facultativo servicio API.';
            return false;
        }

        $isSuccess = $out['success'] ?? false;
        if (!$isSuccess) {
            $this->message = 'Se genero un error al buscar al afiliado facultativo en la API.';
            return false;
        }

        $afiliado = $out['data'] ?? null;

        if ($coddoc == 3 || $coddoc == 7 || $coddoc == 2) {
            $this->message = 'El tipo documento de los afiliados facultativos no es valido, debe solicitar el cambio en tipo documento a la dirección: ' .
                '<b>afiliacionyregistro@comfaca.com</b> indicando la comprobación del estado afiliado facultativo con tipo documento errado.' .
                'No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para poder identificar al afiliado.';

            return false;
        }

        /**
         * buscar usuario de empresa en mercurio
         */
        $usuarioParticular = Mercurio07::where('tipo', 'P')
            ->where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->first();

        $usuarioFacultativo = Mercurio07::where('tipo', $this->tipo)
            ->where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->first();

        if (is_null($afiliado) || $afiliado == false) {
            // ya no está registrado el afiliado empresa
            if ($usuarioFacultativo) {
                $this->estadoAfiliado = 'I';

                return true;
            } else {
                $this->message = 'El facultativo no se encuentra registrado en el sistema principal de Subsidio, no dispone de acceso a la plataforma.';

                return false;
            }
        } else {
            $sucurFacu = false;
            $sucursales = $out['sucursales'] ?? null;
            if ($sucursales) {
                foreach ($sucursales as $ai => $sucursal) {
                    if ($sucursal['calsuc'] == 'F') {
                        $sucurFacu = $sucursal;
                        break;
                    }
                }
            }

            if ($sucurFacu == false) {
                $this->message = 'Error acceso incorrecto. El afiliado facultativo tiene un error de registro en su afiliación, ' .
                    'se debe comunicar a la dirección de correo: <b>afiliacionyregistro@comfaca.com</b> indicando la comprobación del estado afiliado facultativo. ' .
                    'No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para poder identificar al afiliado.';

                return false;
            }
        }
        $this->estadoAfiliado = ($afiliado['estado'] != 'I') ? 'A' : 'I';

        /**
         * Si está registrada la empresa en sisu, en estado inactivo
         */
        if ($afiliado['estado'] == 'I') {

            /**
             * cuando afiliado usuario de mercurio no existe, para inactivos de sisu
             */
            if ($usuarioParticular == false) {

                if (strlen($afiliado['email']) == 0) {
                    $this->message = 'La dirección email no es valida para realizar el registro. ' .
                        'Debe solicitar cambio del correo personal a la dirección <b>afiliacionyregistro@comfaca.com</b> indicando la necesidad. ' .
                        'No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para realizar la comprobación y los cambios solicitados.';

                    return false;
                }

                /**
                 * se crea el usuario en mercurio07
                 */
                $clave = genera_clave(8);
                $hash = clave_hash($clave);
                $crearUsuario = new CrearUsuario;
                $crearUsuario->setters(
                    'tipo: P',
                    "coddoc: {$coddoc}",
                    "documento: {$documento}",
                    "nombre: {$afiliado['razsoc']}",
                    "email: {$afiliado['email']}",
                    "codciu: {$afiliado['codciu']}",
                    "clave: {$hash}"
                );
                $usuarioParticular = $crearUsuario->procesar();

                $key = $this->generaCode();
                $crearUsuario->crearOpcionesRecuperacion($key);
                $this->prepareMail($usuarioParticular, $clave, 'Particular');

                $this->message = 'El afiliado facultativo no está activo en el "SISU", debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. ' .
                    'Es necesario readicar una nueva solicitud de afiliación ya que el facultativo se encuentra <b>Inactivo</b>. ' .
                    'Las credenciales de acceso le serán enviadas al respectivo correo registrado. ' .
                    'Ingresa a la opción 2 de "Afiliación Pendiente"';
            } else {
                /**
                 * Si existe el usuario de mercurio, dado que la empresa está inactiva en sisu.
                 * se inactivan todas las solicitudes vigentes, dado que la empresa este inactiva en sisu
                 */
                $soliPrevias = Mercurio30::where('tipo', $this->tipo)
                    ->where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('estado', 'A')
                    ->first();
                if ($soliPrevias) {
                    $soliPrevias->estado = 'I';
                    $soliPrevias->fecest = date('Y-m-d');
                    $soliPrevias->save();
                }

                $soliPrevTraba = Mercurio31::where('tipo', $this->tipo)
                    ->where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('estado', 'A')
                    ->get();
                if ($soliPrevTraba) {
                    foreach ($soliPrevTraba as $soli) {
                        $soli->estado = 'I';
                        $soli->fecest = date('Y-m-d');
                        $soli->save();
                    }
                }

                $soliPrevCon = Mercurio32::where('tipo', $this->tipo)
                    ->where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('estado', 'A')
                    ->get();
                if ($soliPrevCon) {
                    foreach ($soliPrevCon as $soli) {
                        $soli->estado = 'I';
                        $soli->fecest = date('Y-m-d');
                        $soli->save();
                    }
                }

                $soliPrevBen = Mercurio34::where('tipo', $this->tipo)
                    ->where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('estado', 'A')
                    ->get();
                if ($soliPrevBen) {
                    foreach ($soliPrevBen as $soli) {
                        $soli->estado = 'I';
                        $soli->fecest = date('Y-m-d');
                        $soli->save();
                    }
                }

                if ($usuarioParticular->estado == 'I') {
                    $usuarioParticular->estado = 'A';
                    $usuarioParticular->save();
                }

                $this->message = 'El afiliado no está activo en el "SISU", debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. ' .
                    'Es necesario readicar una nueva solicitud de afiliación ya que el afiliado se encuentra <b>Inactivo</b>. ' .
                    'Ingresa a la opción 2 de "Afiliación Pendiente"';
            }

            /**
             * El afiliado empresa no puede ingresar si está inactivo. ya que no podra realizar más afiliaciones de trabajadores
             * tampoco puede generar certificados
             */
            if ($usuarioFacultativo) {
                $usuarioFacultativo->estado = 'I';
                $usuarioFacultativo->save();
            }

            return false;
        } else {
            /**
             * La empresa está activa en sisu
             */
            if ($usuarioParticular) {
                $usuarioParticular->estado = 'A';
                $usuarioParticular->save();
            }

            if ($usuarioFacultativo) {
                if ($usuarioFacultativo->estado == 'I') {
                    $usuarioFacultativo->estado = 'A';
                    $usuarioFacultativo->save();
                }
            } else {

                $clave = genera_clave(8);
                $hash = clave_hash($clave);
                $crearUsuario = new CrearUsuario;
                $crearUsuario->setters(
                    "tipo: {$this->tipo}",
                    "coddoc: {$coddoc}",
                    "documento: {$documento}",
                    "nombre: {$afiliado['razsoc']}",
                    "email: {$afiliado['email']}",
                    "codciu: {$afiliado['codciu']}",
                    "clave: {$hash}"
                );

                $usuarioFacultativo = $crearUsuario->procesar();
                $key = $this->generaCode();
                $crearUsuario->crearOpcionesRecuperacion($key);
                $this->prepareMail($usuarioFacultativo, $clave);

                $this->message = 'El afiliado está activo y se ha creado de forma automatica la cuenta de Facultativo, ' .
                    'las credenciales de acceso le serán enviadas al respectivo correo registrado, y debe usar la nueva clave generada.';

                return false;
            }

            $this->afiliado = $usuarioFacultativo;

            return true;
        }
    }
}
