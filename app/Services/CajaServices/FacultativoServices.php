<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio36;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class FacultativoServices
{
    private $orderpag = 'ORDER BY fecsol DESC';
    private $tipopc = 10;
    private $tipsoc = '08';
    private $controller_name;


    /**
     * registroSeguimiento variable
     * @var RegistroSeguimiento
     */
    private $registroSeguimiento;

    /**
     * table variable
     * @var Table
     */
    private $table;

    public function __construct()
    {
        $this->table = new Table();
        $this->controller_name = 'aprobacionfac';
        $this->registroSeguimiento = new RegistroSeguimiento();
    }

    /**
     * showTabla function
     * @param object $paginate
     * @return string
     */
    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            "OPT",
            'Días',
            'Identificación',
            'Nombre afiliado',
            'Estado',
            'Fecha solicitud'
        );

        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $style = '#61b5ff';
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) $style = '#d3a246; font-size:1.5em';
                    if ($dias_vencidos > 3) $style = '#ff6161; font-size:1.5em';
                } else {
                    $style = '#344767';
                }
                if ($dias_vencidos == 0) $dias_vencidos = '';

                $id = $entity->getId();
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span>",
                    $entity->getCedtra(),
                    $entity->getPrinom() . ' ' . $entity->getSegnom() . ' ' . $entity->getPriape() . ' ' . $entity->getSegape(),
                    $entity->getEstadoDetalle(),
                    $entity->getFecsol()
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='6'> &nbsp; No hay registros que mostrar</td></tr>");
        }
        return $this->table->generate();
    }

    public function findPagination($query)
    {
        $mercurio36 = new Mercurio36();
        return $mercurio36->find($query, $this->orderpag);
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    public function loadDisplay($mercurio36)
    {
        Tag::displayTo("tipdoc", $mercurio36->getTipdoc());
        Tag::displayTo("nit", $mercurio36->getCedtra());
        Tag::displayTo("id", $mercurio36->getId());
        Tag::displayTo("calemp", $mercurio36->getCalemp());
        Tag::displayTo("telefono", $mercurio36->getTelefono());
        Tag::displayTo("celular", $mercurio36->getCelular());
        Tag::displayTo("email", $mercurio36->getEmail());
        Tag::displayTo("prinom", $mercurio36->getPrinom());
        Tag::displayTo("segnom", $mercurio36->getSegnom());
        Tag::displayTo("priape", $mercurio36->getPriape());
        Tag::displayTo("segape", $mercurio36->getSegape());
        Tag::displayTo("direccion", $mercurio36->getDireccion());
        Tag::displayTo("codact", $mercurio36->getCodact());
        Tag::displayTo("fecini", $mercurio36->getFecini());
        Tag::displayTo("codciu", $mercurio36->getCodciu());
        Tag::displayTo("codzon", $mercurio36->getCodzon());
        Tag::displayTo("coddocrepleg", $mercurio36->getCoddocrepleg());
        Tag::displayTo("subpla", '001');
    }

    /**
     * rechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio36 $mercurio36
     * @param string $nota
     * @param string $codest
     * @return void
     */
    public function rechazar($mercurio36, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio36->getId();
        $mercurio36->setEstado('X');
        $mercurio36->setMotivo($nota);
        $mercurio36->setCodest($codest);
        $mercurio36->setFecest($today->format('Y-m-d'));
        $mercurio36->save();

        $item = (new Mercurio10())->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
        $mercurio10 = new Mercurio10();
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem($item);
        $mercurio10->setEstado("X");
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d'));

        if (!$mercurio10->save()) {
            $msj = "";
            foreach ($mercurio10->getMessages() as $key => $mess) $msj .= $mess->getMessage() . "<br/>";
            throw new DebugException("Error " . $msj, 501);
        }
        return true;
    }

    /**
     * devolver function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio36 $mercurio36
     * @param string $nota
     * @param string $codest
     * @param string $campos_corregir
     * @return void
     */
    public function devolver($mercurio36, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio36->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio36->setEstado('D');
        $mercurio36->setMotivo($nota);
        $mercurio36->setCodest($codest);
        $mercurio36->setFecest($fecest);
        $mercurio36->save();

        $item = (new Mercurio10())->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
        $mercurio10 = new Mercurio10();
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem($item);
        $mercurio10->setEstado("D");
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d'));

        if (!$mercurio10->save()) {
            $msj = "";
            foreach ($mercurio10->getMessages() as $key => $message) $msj .= $message . "<br/>";
            throw new Exception("Error " . $msj, 501);
        }
        (new Mercurio10())->updateAll("campos_corregir='{$campos_corregir}'", "conditions: item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'");
        return true;
    }


    /**
     * msjDevolver function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio36 $mercurio36
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio36, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona: {$mercurio36->getPrinom()} {$mercurio36->getSegnom()} {$mercurio36->getPriape()} {$mercurio36->getSegape()} con identificación: {$mercurio36->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * msjRechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio36 $mercurio36
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio36, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona:  {$mercurio36->getPrinom()} {$mercurio36->getSegnom()} {$mercurio36->getPriape()} {$mercurio36->getSegape()} con identificación: {$mercurio36->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio36
     * @return void
     */
    public function adjuntos($mercurio36)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio36);
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio36
     * @return void
     */
    public function seguimiento($mercurio36)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio36);
    }
}
