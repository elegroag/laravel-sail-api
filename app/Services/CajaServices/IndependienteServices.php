<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio41;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class IndependienteServices
{
    private $orderpag = 'fecsol';
    private $tipopc = 13;
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
        $this->controller_name = 'aprobaindepen';
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
        return Mercurio41::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    public function loadDisplay($mercurio41)
    {
        Tag::displayTo("tipdoc", $mercurio41->getTipdoc());
        Tag::displayTo("nit", $mercurio41->getCedtra());
        Tag::displayTo("id", $mercurio41->getId());
        Tag::displayTo("calemp", $mercurio41->getCalemp());
        Tag::displayTo("telefono", $mercurio41->getTelefono());
        Tag::displayTo("celular", $mercurio41->getCelular());
        Tag::displayTo("email", $mercurio41->getEmail());
        Tag::displayTo("prinom", $mercurio41->getPrinom());
        Tag::displayTo("segnom", $mercurio41->getSegnom());
        Tag::displayTo("priape", $mercurio41->getPriape());
        Tag::displayTo("segape", $mercurio41->getSegape());
        Tag::displayTo("direccion", $mercurio41->getDireccion());
        Tag::displayTo("codact", $mercurio41->getCodact());
        $fecini = $mercurio41->getFecini();
        Tag::displayTo("fecini", ($fecini instanceof Carbon) ? $fecini->format('Y-m-d') : $fecini);
        Tag::displayTo("codciu", $mercurio41->getCodciu());
        Tag::displayTo("codzon", $mercurio41->getCodzon());
        Tag::displayTo("coddocrepleg", $mercurio41->getCoddocrepleg());
        Tag::displayTo("subpla", '001');
    }

    public function rechazar($mercurio41, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio41->getId();
        $mercurio41->setEstado('X');
        $mercurio41->setMotivo($nota);
        $mercurio41->setCodest($codest);
        $mercurio41->setFecest($today->format('Y-m-d'));
        $mercurio41->save();

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

    public function devolver($mercurio41, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio41->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio41->setEstado('D');
        $mercurio41->setMotivo($nota);
        $mercurio41->setCodest($codest);
        $mercurio41->setFecest($fecest);
        $mercurio41->save();

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

    public function msjDevolver($mercurio41, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona: {$mercurio41->getPrinom()} {$mercurio41->getSegnom()} {$mercurio41->getPriape()} {$mercurio41->getSegape()} con identificación: {$mercurio41->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    public function msjRechazar($mercurio41, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona:  {$mercurio41->getPrinom()} {$mercurio41->getSegnom()} {$mercurio41->getPriape()} {$mercurio41->getSegape()} con identificación: {$mercurio41->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio41
     * @return void
     */
    public function adjuntos($mercurio41)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio41);
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio41
     * @return void
     */
    public function seguimiento($mercurio41)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio41);
    }
}
