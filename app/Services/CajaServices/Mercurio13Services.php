<?php

namespace App\Services\CajaServices;

use App\Models\Mercurio13;
use App\Services\Utils\Table;

class Mercurio13Services
{

    /**
     * table variable
     * @var Table
     */
    private $table;

    /**
     * controller_name variable
     * @var string
     */
    private $controller_name;

    public function __construct()
    {

        $this->table = new Table();
        $this->controller_name = 'mercurio13';
    }

    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            array(
                'OPT',
                'Tipo afiliación',
                'Tipo documento',
                'Obligatorio',
                'Auto generado'
            )
        );
        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $coddoc = $entity->getCoddoc();
                $tipopc = $entity->getTipopc();
                $this->table->add_row(
                    "<a data-toggle='info' data-coddoc='{$coddoc}' data-tipopc='{$tipopc}' class='btn btn-xs ml-1 btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>
                    <a data-toggle='borrar' data-coddoc='{$coddoc}' data-tipopc='{$tipopc}' class='btn btn-xs btn-danger text-white' title='Borrar'> <i class='fas fa-trash text-white'></i></a>",
                    $entity->getMercurio09()->getDetalle(),
                    $entity->getMercurio12()->getDetalle(),
                    ($entity->getObliga() == 'S') ? 'Sí' : 'No',
                    ($entity->getAuto_generado() == '1') ? 'Sí' : 'No'
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='5'> &nbsp; No hay registros que mostrar</td></tr>");
        }
        return $this->table->generate();
    }

    public function findPagination($query)
    {
        return (new Mercurio13())->find($query);
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }
}
