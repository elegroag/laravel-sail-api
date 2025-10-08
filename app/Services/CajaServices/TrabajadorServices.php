<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio31;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class TrabajadorServices
{
    private $orderpag = 'fecsol';
    private $tipopc = 1;
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
        $this->controller_name = 'aprobaciontra';
        $this->registroSeguimiento = new RegistroSeguimiento();
    }

    /**
     * findPagination function
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $query
     * @return void
     */
    public function findPagination($query)
    {
        return Mercurio31::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    /**
     * showTabla function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param object $paginate
     * @return void
     */
    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            "OPT",
            'Días',
            'Identificación',
            'Nombre afiliado',
            'NIT',
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
                    $entity->getNit(),
                    $entity->getEstadoDetalle(),
                    $entity->getFecsol()
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

    /**
     * loadDisplay function
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio31 $mercurio31
     * @return void
     */
    public function loadDisplay($mercurio31)
    {
        Tag::displayTo("tipdoc", $mercurio31->getTipdoc());
        Tag::displayTo("nit", $mercurio31->getNit());
        Tag::displayTo("cedtra", $mercurio31->getCedtra());
        Tag::displayTo("id", $mercurio31->getId());
        Tag::displayTo("telefono", $mercurio31->getTelefono());
        Tag::displayTo("celular", $mercurio31->getCelular());
        Tag::displayTo("email", $mercurio31->getEmail());
        Tag::displayTo("prinom", $mercurio31->getPrinom());
        Tag::displayTo("segnom", $mercurio31->getSegnom());
        Tag::displayTo("priape", $mercurio31->getPriape());
        Tag::displayTo("segape", $mercurio31->getSegape());
        Tag::displayTo("direccion", $mercurio31->getDireccion());
        $fecsol = $mercurio31->getFecsol();
        Tag::displayTo("fecsol", ($fecsol instanceof Carbon) ? $fecsol->format('Y-m-d') : $fecsol);
        Tag::displayTo("codciu", $mercurio31->getCodciu());
        Tag::displayTo("codzon", $mercurio31->getCodzon());
    }

    /**
     * rechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio31
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($mercurio31, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio31->getId();
        $mercurio31->setEstado('X');
        $mercurio31->setMotivo($nota);
        $mercurio31->setCodest($codest);
        $mercurio31->setFecest($today->format('Y-m-d'));
        $mercurio31->save();

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
     * @param [type] $mercurio31
     * @param [type] $nota
     * @param [type] $codest
     * @param string $campos_corregir
     * @return void
     */
    public function devolver($mercurio31, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio31->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio31->setEstado('D');
        $mercurio31->setMotivo($nota);
        $mercurio31->setCodest($codest);
        $mercurio31->setFecest($fecest);
        $mercurio31->save();

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
     * @param [type] $mercurio31
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio31, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por el trabajador: {$mercurio31->getPrinom()} {$mercurio31->getSegnom()} {$mercurio31->getPriape()} {$mercurio31->getSegape()} con identificación: {$mercurio31->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * msjRechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio31
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio31, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por el trabajador:  {$mercurio31->getPrinom()} {$mercurio31->getSegnom()} {$mercurio31->getPriape()} {$mercurio31->getSegape()} con identificación: {$mercurio31->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio31
     * @return void
     */
    public function adjuntos($mercurio31)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio31);
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio31
     * @return void
     */
    public function seguimiento($mercurio31)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio31);
    }
}
