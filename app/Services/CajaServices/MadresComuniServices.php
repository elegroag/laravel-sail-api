<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio39;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class MadresComuniServices
{

    private $orderpag = 'fecsol';
    private $tipopc = 11;
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
        $this->controller_name = 'aprobacioncom';
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
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId(), $entity->getFecsol());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) $style = '#d3a246; font-size:1.3em';
                    if ($dias_vencidos > 3) $style = '#ff6161; font-size:1.3em';
                } else {
                    $style = '#344767';
                }
                if ($dias_vencidos == 0) $dias_vencidos = '';

                $id = $entity->getId();
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span> ",
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
        return Mercurio39::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    public function loadDisplay($mercurio39)
    {
        Tag::displayTo("tipdoc", $mercurio39->getTipdoc());
        Tag::displayTo("nit", $mercurio39->getCedtra());
        Tag::displayTo("id", $mercurio39->getId());
        Tag::displayTo("calemp", $mercurio39->getCalemp());
        Tag::displayTo("telefono", $mercurio39->getTelefono());
        Tag::displayTo("celular", $mercurio39->getCelular());
        Tag::displayTo("email", $mercurio39->getEmail());
        Tag::displayTo("prinom", $mercurio39->getPrinom());
        Tag::displayTo("segnom", $mercurio39->getSegnom());
        Tag::displayTo("priape", $mercurio39->getPriape());
        Tag::displayTo("segape", $mercurio39->getSegape());
        Tag::displayTo("direccion", $mercurio39->getDireccion());
        Tag::displayTo("codact", $mercurio39->getCodact());
        Tag::displayTo("codciu", $mercurio39->getCodciu());
        Tag::displayTo("codzon", $mercurio39->getCodzon());
        Tag::displayTo("coddocrepleg", $mercurio39->getCoddocrepleg());
        Tag::displayTo("subpla", '001');
    }

    /**
     * rechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio39 $mercurio39
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($mercurio39, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio39->getId();
        $mercurio39->setEstado('X');
        $mercurio39->setMotivo($nota);
        $mercurio39->setCodest($codest);
        $mercurio39->setFecest($today->format('Y-m-d'));
        $mercurio39->save();

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
     * @changed [2023-12-21]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio39 $mercurio39
     * @param [type] $nota
     * @param [type] $codest
     * @param string $campos_corregir
     * @return void
     */
    public function devolver($mercurio39, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio39->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio39->setEstado('D');
        $mercurio39->setMotivo($nota);
        $mercurio39->setCodest($codest);
        $mercurio39->setFecest($fecest);
        $mercurio39->save();

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
     * @changed [2023-12-21]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio39 $mercurio39
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio39, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona: {$mercurio39->getPrinom()} {$mercurio39->getSegnom()} {$mercurio39->getPriape()} {$mercurio39->getSegape()} con identificación: {$mercurio39->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * msjRechazar function
     * @changed [2023-12-21]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio39 $mercurio39
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio39, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona:  {$mercurio39->getPrinom()} {$mercurio39->getSegnom()} {$mercurio39->getPriape()} {$mercurio39->getSegape()} con identificación: {$mercurio39->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio39
     * @return void
     */
    public function adjuntos($mercurio39)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio39);
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio39
     * @return void
     */
    public function seguimiento($mercurio39)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio39);
    }
}
