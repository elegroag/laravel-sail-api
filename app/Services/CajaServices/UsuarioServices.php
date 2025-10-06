<?php

namespace App\Services\CajaServices;

use App\Exceptions\AuthException;
use App\Models\Gener02;
use App\Models\Gener21;
use App\Models\Mercurio07;
use App\Services\Utils\Comman;
use App\Services\Utils\Table;

class UsuarioServices
{
    protected $procesadorComando;
    protected $orderpag;

    /**
     * table variable
     * @var Table
     */
    private $table;

    public function __construct()
    {
        $this->procesadorComando = Comman::Api();
        $this->table = new Table();
    }

    public function actualizaUsuario($datos)
    {
        $ngener21 = Gener21::where('tipfun', $datos->tipfun);
        if ($ngener21->exists()) {
            $ngener21->update([
                'detalle' => $datos->tipfun_detalle
            ]);
        } else {
            $gener21 = new Gener21();
            $gener21->setTipfun($datos->tipfun);
            $gener21->setDetalle($datos->tipfun_detalle);
            $gener21->save();
        }

        $ngener02 = Gener02::where('usuario', $datos->usuario);
        if ($ngener02->exists()) {
            $ngener02->update([
                'nombre' => $datos->nombre,
                'tipfun' => $datos->tipfun,
                'email' => $datos->estacion,
                'login' => $datos->login,
                'criptada' => $datos->criptada,
                'cedtra' => $datos->cedtra,
                'estado' => $datos->estado
            ]);
        } else {
            $gener02 = new Gener02();
            $gener02->setUsuario($datos->usuario);
            $gener02->setNombre($datos->nombre);
            $gener02->setTipfun($datos->tipfun);
            $gener02->setEmail($datos->estacion);
            $gener02->setLogin($datos->login);
            $gener02->setCriptada($datos->criptada);
            $gener02->setCedtra($datos->cedtra);
            $gener02->setEstado($datos->estado);
            $gener02->save();
        }
    }

    public function buscarUsuarioByUser($user)
    {
        $this->procesadorComando->runCli(
            array(
                "servicio" => "Usuarios",
                "metodo" => "trae_usuario",
                "params" => $user
            )
        );

        $salida = $this->procesadorComando->toArray();
        $usuario = (object) $salida['data'];
        if (!$usuario) {
            throw new AuthException("El usuario no es correcto para continuar con la autenticación. 4", 4);
        }
        if ($usuario->estado == 'B') {
            throw new AuthException("El usuario se encuentra bloqueado, por fallar en la autenticación con más de 3 intentos." .
                " Para poder desbloquear su cuenta puede recuperar la cuenta de usuario o solicitar el desbloqueo de su cuenta, " .
                "al aréa de sistemas, soporte_sistemas@comfaca.com.", 5);
        }
        return $usuario;
    }

    public function findPagination($query)
    {
        $mercurio07 = new Mercurio07();
        return $mercurio07->find($query, $this->orderpag);
    }

    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            "OPT",
            'Tipo documento',
            'Identificación',
            'Nombre',
            'Email',
            'Estado',
            'Tipo'
        );

        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $this->table->add_row(
                    "<a data-cid='{$entity->getDocumento()}' data-tipo='{$entity->getTipo()}' data-coddoc='{$entity->getCoddoc()}' data-toggle='info' class='btn btn-xs btn-primary' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>
                     <a data-cid='{$entity->getDocumento()}' data-tipo='{$entity->getTipo()}' data-coddoc='{$entity->getCoddoc()}' data-toggle='borrar'  class='btn btn-xs btn-danger' title='Borrar'> <i class='fas fa-trash text-black'></i></a>",
                    $entity->getCoddocDetalle(),
                    $entity->getDocumento(),
                    $entity->getNombre(),
                    strtolower($entity->getEmail()),
                    $entity->getEstadoDetalle(),
                    $entity->getTipoDetalle()
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='7'> &nbsp; No hay registros que mostrar</td></tr>");
        }
        return $this->table->generate();
    }

    /**
     * getTemplateTable function
     * @changed [2023-12-19]

     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }
}
