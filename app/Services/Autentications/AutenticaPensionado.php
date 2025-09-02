<?php

namespace App\Services\Autentications;

use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Utils\Generales;
use App\Services\Utils\CrearUsuario;

class AutenticaPensionado extends AutenticaGeneral
{
    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'O';
        $this->tipoName = 'Pensionado';
    }

    public function comprobarSISU($documento, $coddoc)
    {
        /**
         * buscar empresa en sisu
         */
        $this->procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" =>  array(
                    "nit" => $documento
                )
            )
        );

        if ($this->procesadorComando->isJson() == False) {
            $this->message = "Se genero un error al buscar al afiliado pensionado usando el servicio CLI-Comando.";
            return false;
        }

        $out = $this->procesadorComando->toArray();
        $afiliado = ($out['success'] == true) ? $out['data'] : null;

        if (is_null($afiliado)) {
            //ya no está registrado el afiliado empresa
            $this->message = "Error acceso incorrecto. El afiliado pensionado no está activo en el \"SISU\", para su ingreso a la plataforma.";
            return false;
        }

        $sucurPension = False;
        $sucursales = $out['sucursales'];
        if ($sucursales) {
            foreach ($sucursales as $ai => $sucursal) {
                if (($sucursal['calsuc'] == 'A' || $sucursal['calsuc'] == 'P')) {
                    $sucurPension = $sucursal;
                    break;
                }
            }
        }

        if ($sucurPension == False) {
            $this->message = "Error acceso incorrecto. El afiliado pensionado tiene un error de registro en su afiliación, " .
                "se debe comunicar a la dirección de correo: <b>afiliacionyregistro@comfaca.com</b> indicando la comprobación del estado afiliado pensionado. " .
                "No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para poder identificar al afiliado.";
            return false;
        }

        if ($coddoc == 3 || $coddoc == 7 || $coddoc == 2) {
            $this->message = "El tipo documento del afiliado pensionado no es valido, debe solicitar el cambio en tipo documento a la dirección: " .
                "<b>afiliacionyregistro@comfaca.com</b> indicando la comprobación del estado afiliado pensionado con tipo documento errado." .
                "No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para poder identificar al afiliado.";
            return false;
        }

        if ($afiliado['coddoc'] != $coddoc) {
            $this->message = "El tipo documento del afiliado pensionado no es valido, debe solicitar el cambio en tipo documento a la dirección: " .
                "<b>afiliacionyregistro@comfaca.com</b> indicando la comprobación del estado afiliado pensionado con tipo documento errado." .
                "No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para poder identificar al afiliado.";
            return false;
        }

        /**
         * buscar usuario de empresa en mercurio
         */
        $usuarioParticular = (new Mercurio07)->findFirst("tipo='P' AND documento='{$documento}' AND coddoc='{$coddoc}'");

        $usuarioPensionado = (new Mercurio07)->findFirst("tipo='O' AND documento='{$documento}' AND coddoc='{$coddoc}'");

        /**
         * Si está registrada la empresa en sisu, en estado inactivo
         */
        if ($afiliado['estado'] == 'I') {

            /**
             * cuando afiliado usuario de mercurio no existe, para inactivos de sisu
             */
            if ($usuarioParticular == false) {

                if (strlen($afiliado['email']) == 0) {
                    $this->message = "La dirección email no es valida para realizar el registro. " .
                        "Debe solicitar cambio del correo personal a la dirección <b>afiliacionyregistro@comfaca.com</b> indicando la necesidad. " .
                        "No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para realizar la comprobación y los cambios solicitados.";
                    return false;
                }

                /**
                 * se crea el usuario en mercurio07
                 */
                list($hash, $clave) = Generales::GeneraClave();
                $crearUsuario = new CrearUsuario();
                $crearUsuario->setters(
                    "tipo: P",
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
                $this->prepareMail($usuarioParticular, $clave, "Particular");

                $this->message = "El afiliado pensionado no está activo en el \"SISU\", debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. " .
                    "Es necesario readicar una nueva solicitud de afiliación ya que el pensionado se encuentra <b>Inactivo</b>. " .
                    "Las credenciales de acceso le serán enviadas al respectivo correo registrado. " .
                    "Ingresa a la opción 2 de \"Afiliación Pendiente\"";
            } else {
                /**
                 * Si existe el usuario de mercurio, dado que la empresa está inactiva en sisu.
                 * se inactivan todas las solicitudes vigentes, dado que la empresa este inactiva en sisu
                 */
                $soliPrevias = (new Mercurio30)->findFirst("tipo='I' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevias) {
                    $soliPrevias->setEstado('I');
                    $soliPrevias->setFecest(date('Y-m-d'));
                    $soliPrevias->save();
                }

                $soliPrevTraba = (new Mercurio31)->find("tipo='I' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevTraba) {
                    foreach ($soliPrevTraba as $soli) {
                        $soli->setEstado('I');
                        $soli->setFecest(date('Y-m-d'));
                        $soli->save();
                    }
                }

                $soliPrevCon = (new Mercurio32)->find("tipo='I' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevCon) {
                    foreach ($soliPrevCon as $soli) {
                        $soli->setEstado('I');
                        $soli->setFecest(date('Y-m-d'));
                        $soli->save();
                    }
                }

                $soliPrevBen = (new Mercurio34)->find("tipo='I' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevBen) {
                    foreach ($soliPrevBen as $soli) {
                        $soli->setEstado('I');
                        $soli->setFecest(date('Y-m-d'));
                        $soli->save();
                    }
                }

                if ($usuarioParticular->getEstado() == 'I') {
                    $usuarioParticular->setEstado('A');
                    $usuarioParticular->save();
                }

                $this->message = "El afiliado no está activo en el \"SISU\", debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. " .
                    "Es necesario readicar una nueva solicitud de afiliación ya que el afiliado se encuentra <b>Inactivo</b>. " .
                    "Ingresa a la opción 2 de \"Afiliación Pendiente\"";
            }

            /**
             * El afiliado empresa no puede ingresar si está inactivo. ya que no podra realizar más afiliaciones de trabajadores
             * tampoco puede generar certificados
             */
            if ($usuarioPensionado) {
                $usuarioPensionado->setEstado('I');
                $usuarioPensionado->save();
            }
            return false;
        } else {
            /**
             * La empresa está activa en sisu
             */
            if ($usuarioParticular) {
                $usuarioParticular->setEstado('A');
                $usuarioParticular->save();
            }

            if ($usuarioPensionado) {
                if ($usuarioPensionado->getEstado() == 'I') {
                    $usuarioPensionado->setEstado('A');
                    $usuarioPensionado->save();
                }
            } else {

                list($hash, $clave) = Generales::GeneraClave();
                $crearUsuario = new CrearUsuario();
                $crearUsuario->setters(
                    "tipo: O",
                    "coddoc: {$coddoc}",
                    "documento: {$documento}",
                    "nombre: {$afiliado['razsoc']}",
                    "email: {$afiliado['email']}",
                    "codciu: {$afiliado['codciu']}",
                    "clave: {$hash}"
                );

                $usuarioPensionado = $crearUsuario->procesar();

                $key = $this->generaCode();
                $crearUsuario->crearOpcionesRecuperacion($key);
                $this->prepareMail($usuarioPensionado, $clave);

                $this->message = "El afiliado está activo y se ha creado de forma automatica la cuenta de Pensionado, " .
                    "las credenciales de acceso le serán enviadas al respectivo correo registrado, y debe usar la nueva clave generada.";
                return false;
            }

            $this->afiliado = $usuarioPensionado;
            return true;
        }
    }
}
