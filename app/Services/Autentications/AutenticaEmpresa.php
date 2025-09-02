<?php
namespace App\Services\Autentications;
use App\Models\Mercurio07;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Services\Utils\Generales;
use CrearUsuario;

class AutenticaEmpresa extends AutenticaGeneral
{

    public function __construct()
    {
        parent::__construct();
        $this->tipo = 'E';
        $this->tipoName = 'Empresa aportante';
    }

    /**
     * comprobarSISU function
     * autenticar empresas, en sesion consulta y gestion,
     * comprobar que la empresa este registrada en SISU
     * comprueba que este el usuario de la empresa en mercurio
     * hace los registro de forma automatica
     * @param [type] $documento
     * @param [type] $coddoc
     * @return bool
     */
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
                    "nit" => $documento,
                    "coddoc" => $coddoc
                )
            )
        );

        if ($this->procesadorComando->isJson() === False) {
            $this->message = "Se genero un error al buscar la empresa usando el servicio CLI-Comando. ";
            return false;
        }

        $out = $this->procesadorComando->toArray();
        $afiliado = ($out['success'] == true && isset($out['data']) && $out['data'] != false) ? $out['data'] : null;

        if (is_null($afiliado)) {
            //ya no está registrado el afiliado empresa
            $this->message = "Error acceso incorrecto. La empresa no está activa en el \"SISU\", para su ingreso a la plataforma.";
            return false;
        }

        /**
         * buscar usuario de empresa en mercurio
         */
        $usuarioParticular = (new Mercurio07)->findFirst("tipo='P' AND documento='{$documento}' AND coddoc='{$coddoc}'");
        $usuarioEmpresa = (new Mercurio07)->findFirst("tipo='{$this->tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}'");

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
                        "Debe solicitar cambio del correo personal a la dirección afiliacionyregistro@comfaca.com, indicando la necesidad. " .
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

                $codigoVerify = $this->generaCode();
                $crearUsuario->crearOpcionesRecuperacion($codigoVerify);

                $this->prepareMail($usuarioParticular, $clave, "Particular");

                $this->message = "La empresa no está activa en el sistema principal de Subsidio, debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. " .
                    "Es necesario readicar una nueva solicitud de afiliación ya que la empresa se encuentra (INACTIVA)." .
                    "Las credenciales de acceso le serán enviadas al respectivo correo registrado.";
            } else {
                /**
                 * Si existe el usuario de mercurio, dado que la empresa está inactiva en sisu.
                 * se inactivan todas las solicitudes vigentes, dado que la empresa este inactiva en sisu
                 */
                $soliPrevias = (new Mercurio30)->findFirst("tipo='{$this->tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevias) {
                    $soliPrevias->setEstado('I');
                    $soliPrevias->setFecest(date('Y-m-d'));
                    $soliPrevias->save();
                }

                $soliPrevTraba = (new Mercurio31)->find("tipo='{$this->tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevTraba) {
                    foreach ($soliPrevTraba as $soli) {
                        $soli->setEstado('I');
                        $soli->setFecest(date('Y-m-d'));
                        $soli->save();
                    }
                }

                $soliPrevCon = (new Mercurio32)->find("tipo='{$this->tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
                if ($soliPrevCon) {
                    foreach ($soliPrevCon as $soli) {
                        $soli->setEstado('I');
                        $soli->setFecest(date('Y-m-d'));
                        $soli->save();
                    }
                }

                $soliPrevBen = (new Mercurio34)->find("tipo='{$this->tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado='A'");
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

                $this->message = "La empresa no está activa en el sistema principal de Subsidio, debe realizar el proceso de afiliación, para acceder a los servicios de comfaca en línea. " .
                    "Es necesario radicar una nueva solicitud de afiliación ya que la empresa se encuentra (INACTIVA)";
            }

            /**
             * El afiliado empresa no puede ingresar si está inactivo. ya que no podra realizar más afiliaciones de trabajadores
             * tampoco puede generar certificados
             */
            if ($usuarioEmpresa) {
                $usuarioEmpresa->setEstado('I');
                $usuarioEmpresa->save();
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

            if ($usuarioEmpresa) {
                if ($usuarioEmpresa->getEstado() == 'I') {
                    $usuarioEmpresa->setEstado('A');
                    $usuarioEmpresa->save();
                }
            } else {

                list($hash, $clave) = Generales::GeneraClave();
                $crearUsuario = new CrearUsuario();
                $crearUsuario->setters(
                    "tipo: E",
                    "coddoc: {$coddoc}",
                    "documento: {$documento}",
                    "nombre: {$afiliado['razsoc']}",
                    "email: {$afiliado['email']}",
                    "codciu: {$afiliado['codciu']}",
                    "clave: {$hash}"
                );
                $usuarioEmpresa = $crearUsuario->procesar();

                $codigoVerify = $this->generaCode();
                $crearUsuario->crearOpcionesRecuperacion($codigoVerify);

                $this->prepareMail($usuarioEmpresa, $clave);
                $this->message = "La empresa está activa y se ha creado de forma automatica la cuenta de usuario tipo Empresa, " .
                    "las credenciales de acceso le serán enviadas al respectivo correo registrado, y debe usar la nueva clave generada.";
                return false;
            }

            $this->afiliado = $usuarioEmpresa;
            return true;
        }
    }
}
