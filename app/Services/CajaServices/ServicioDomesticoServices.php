<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio40;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class ServicioDomesticoServices
{

    private $orderpag = 'fecsol';
    private $tipopc = 12;
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
        $this->controller_name = 'aprobaciondom';
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
        return Mercurio40::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    public function loadDisplay($mercurio40)
    {
        Tag::displayTo("tipdoc", $mercurio40->getTipdoc());
        Tag::displayTo("nit", $mercurio40->getCedtra());
        Tag::displayTo("id", $mercurio40->getId());
        Tag::displayTo("calemp", $mercurio40->getCalemp());
        Tag::displayTo("telefono", $mercurio40->getTelefono());
        Tag::displayTo("celular", $mercurio40->getCelular());
        Tag::displayTo("email", $mercurio40->getEmail());
        Tag::displayTo("prinom", $mercurio40->getPrinom());
        Tag::displayTo("segnom", $mercurio40->getSegnom());
        Tag::displayTo("priape", $mercurio40->getPriape());
        Tag::displayTo("segape", $mercurio40->getSegape());
        Tag::displayTo("direccion", $mercurio40->getDireccion());
        Tag::displayTo("codact", $mercurio40->getCodact());
        Tag::displayTo("codciu", $mercurio40->getCodciu());
        Tag::displayTo("codzon", $mercurio40->getCodzon());
        Tag::displayTo("coddocrepleg", $mercurio40->getCoddocrepleg());
        Tag::displayTo("subpla", '001');
    }

    /**
     * rechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio40 $mercurio40
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($mercurio40, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio40->getId();
        $mercurio40->setEstado('X');
        $mercurio40->setMotivo($nota);
        $mercurio40->setCodest($codest);
        $mercurio40->setFecest($today->format('Y-m-d'));
        $mercurio40->save();

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
     * @param Mercurio40 $mercurio40
     * @param [type] $nota
     * @param [type] $codest
     * @param string $campos_corregir
     * @return void
     */
    public function devolver($mercurio40, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio40->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio40->setEstado('D');
        $mercurio40->setMotivo($nota);
        $mercurio40->setCodest($codest);
        $mercurio40->setFecest($fecest);
        $mercurio40->save();

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
     * @param Mercurio40 $mercurio40
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio40, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona: {$mercurio40->getPrinom()} {$mercurio40->getSegnom()} {$mercurio40->getPriape()} {$mercurio40->getSegape()} con identificación: {$mercurio40->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * msjRechazar function
     * @changed [2023-12-21]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio40 $mercurio40
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio40, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por la persona:  {$mercurio40->getPrinom()} {$mercurio40->getSegnom()} {$mercurio40->getPriape()} {$mercurio40->getSegape()} con identificación: {$mercurio40->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio40
     * @return void
     */
    public function adjuntos($mercurio40)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio40);
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio40
     * @return void
     */
    public function seguimiento($mercurio40)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio40);
    }
}
