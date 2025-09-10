<?php

namespace App\Services\SatApi;

use App\Exceptions\DebugException;
use App\Services\Utils\Comman;

class SatApiServices
{
    private $response;
    /**
     * procesadorComando variable
     *
     * @var ProcesadorComando
     */
    private $procesadorComando;

    public function __construct()
    {
        $this->procesadorComando = Comman::Api();
    }

    /**
     * afiliaTrabajador function
     * @changed [2023-12-00]
     *  Inicia relacion laboral
     * @author elegroag <elegroag@ibero.edu.co>
     * @param integer $numdoctra
     * @return array
     */
    public function afiliaTrabajador($numdoctra)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "procesa_trabajador",
                    "params" => array(
                        'cedtra' => $numdoctra
                    )
                )
            );
            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }
            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    /**
     * empresaNueva function
     * @changed [2023-12-00]
     * Afiliación primera vez
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $numdocemp
     * @return array
     */
    public function empresaNueva($numdocemp)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "empresa_nueva",
                    "params" => array(
                        'nit' => $numdocemp
                    )
                )
            );

            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }

            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    /**
     * empresaReintegro function
     * @changed [2023-12-00]
     * Afiliacion no primera vez
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $numdocemp
     * @return void
     */
    public function empresaReintegro($numdocemp)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "empresa_reintegro",
                    "params" => array(
                        'nit' => $numdocemp
                    )
                )
            );

            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }

            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    /**
     * empresaRetiro function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $numdocemp
     * @return void
     */
    public function empresaRetiro($numdocemp)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "empresa_retiro",
                    "params" => array(
                        'nit' => $numdocemp
                    )
                )
            );

            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }

            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    /**
     * terminaTrabajador function
     * @changed [2023-12-00]
     * Trabajador retirado
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $cedtra
     * @return void
     */
    public function terminaTrabajador($cedtra)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "termina_trabajador",
                    "params" => array(
                        'cedtra' => $cedtra
                    )
                )
            );

            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }

            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    /**
     * consultaEmpresaEmpleados function
     * @changed [2023-12-00]
     * Trabajador retirado
     * @author elegroag <elegroag@ibero.edu.co>
     * @param integer $nit
     * @return void
     */
    public function consultaEmpresaEmpleados($nit)
    {
        try {
            $this->procesadorComando->runCli(
                array(
                    "servicio" => "ServicioSat",
                    "metodo" => "consulta_empresa_empleados",
                    "params" => array(
                        'nit' => $nit
                    )
                )
            );
            if ($this->procesadorComando->isJson() == False) {
                throw new DebugException("Error al dar respuesta al servicio de solicitud sat", 1);
            }
            $this->response = $this->procesadorComando->toArray();
        } catch (DebugException $error) {
            $this->response = $error->getMessage();
            return false;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }
}
