<?php

namespace App\Services\Autentications;

use App\Models\Mercurio07;
use App\Services\Utils\CrearUsuario;

class AutenticaTrabajador extends AutenticaGeneral
{
    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'T';
        $this->tipoName = 'Trabajador';
    }

    /**
     * comprobarSISU function
     * autenticar trabajador, en sesion consulta y gestion,
     * comprobar que la trabajador este registrada en SISU
     * comprueba que este el usuario de la trabajador en mercurio
     * hace los registro de forma automatica
     *
     * @param [type] $documento
     * @param [type] $coddoc
     * @return bool
     */
    public function comprobarSISU($documento, $coddoc)
    {
        $this->procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $documento,
                    'coddoc' => $coddoc,
                ],
            ]
        );

        if ($this->procesadorComando->isJson() == false) {
            $this->message = 'Se genero un error al buscar al trabajador usando el servicio CLI-Comando. ';

            return false;
        }

        $out = $this->procesadorComando->toArray();

        $afiliado = ($out['success']) ? $out['data'] : false;

        // / Si se encuentra el trabajador
        $usuarioTrabajador = Mercurio07::where('tipo', 'T')
            ->where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->first();

        if ($afiliado == null || $afiliado == false) {
            if ($usuarioTrabajador) {
                $this->estadoAfiliado = 'I';

                return true;
            } else {
                $this->message = 'El trabajador no se encuentra registrado en el sistema principal de Subsidio, no dispone de acceso a la plataforma.';

                return false;
            }
        }

        $this->estadoAfiliado = $afiliado['estado'];

        // trabajadores no inactivos y no muertos
        if ($afiliado['estado'] == 'I' || $afiliado['estado'] == 'M') {

            $this->message = 'El trabajador se encuentra actualmente (INACTIVO) en el sistema principal de Subsidio, '.
                'dispone de acceso a la plataforma, pero no puede solicitar afiliación de beneficiarios, conyuges, actualización de datos y otros servicios que ofrece COMFACA.';
            $this->afiliado = $usuarioTrabajador;

            return true;
        } else {
            /**
             * Trabajador activo
             * Si no está registrado trabajador
             */
            if ($usuarioTrabajador == false) {

                if (strlen($afiliado['email']) > 0) {
                    $nombre = strtoupper($afiliado['prinom'].' '.$afiliado['segnom'].' '.$afiliado['priape'].' '.$afiliado['segape']);
                    $codzon = ($afiliado['codzon'] == '') ? 18001 : $afiliado['codzon'];

                    $clave = genera_clave(8);
                    $hash = clave_hash($clave);

                    $crearUsuario = new CrearUsuario;
                    $crearUsuario->setters(
                        'tipo: T',
                        "coddoc: {$coddoc}",
                        "documento: {$documento}",
                        "nombre: {$nombre}",
                        "email: {$afiliado['email']}",
                        "codciu: {$codzon}",
                        "clave: {$hash}"
                    );
                    $usuarioTrabajador = $crearUsuario->procesar();

                    $key = $this->generaCode();
                    $crearUsuario->crearOpcionesRecuperacion($key);
                    $this->prepareMail($usuarioTrabajador, $clave);

                    $this->message = 'El trabajador se encuentra actualmente activo en el sistema principal de Subsidio, '.
                        'las credenciales de acceso le serán enviadas al respectivo correo registrado, y debe usar la nueva clave generada.';

                    return false;
                } else {

                    $this->message = 'La dirección email no es valida para realizar el registro. '.
                        'Debe solicitar cambio del correo personal a la dirección afiliacionyregistro@comfaca.com indicando la necesidad. '.
                        'No olvidar el compartir la dirección email, el número de cedula y el nombre del afiliado, para realizar la comprobación y los cambios solicitados.';

                    return false;
                }
            } else {
                if ($usuarioTrabajador) {
                    $usuarioTrabajador->setEstado('A');
                    $usuarioTrabajador->save();
                }
                $this->message = 'El trabajador se encuentra actualmente (INACTIVO) en el sistema principal de Subsidio, no dispone de acceso a la plataforma.';
                $this->afiliado = $usuarioTrabajador;

                return true;
            }
        }
    }
}
