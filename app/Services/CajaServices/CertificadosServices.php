<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio45;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class CertificadosServices
{
    private $orderpag = 'fecha';
    private $tipopc = 8;
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
        $this->controller_name = 'aprobacioncer';
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
        return Mercurio45::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
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
        $mercurio01 = (new Mercurio01)->findFirst();
        $this->table->set_template(Table::TmpGeneral());

        $this->table->set_heading(
            "OPT",
            'Días',
            'Trabajador',
            'Fecha',
            'Certificado'
        );
        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $style = '#61b5ff';
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId(), $entity->getFecha());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) $style = '#d3a246; font-size:1.3em';
                    if ($dias_vencidos > 3) $style = '#ff6161; font-size:1.3em';
                } else {
                    $style = '#344767';
                }
                if ($dias_vencidos == 0) $dias_vencidos = '';
                $id = $entity->getId();

                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>" .
                        "<a data-cid='{$id}' data-toggle='file' class='btn btn-xs btn-success text-white' data-path='{$mercurio01->getPath()}' data-file='{$entity->getArchivo()}'>" .
                        "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span></a>",
                    "<i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span>",
                    $entity->getCedtra() . ' | ' . $entity->getNombre(),
                    $entity->getFecha(),
                    $entity->getNomcer()
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='6'> &nbsp; No hay registros que mostrar</td></tr>");
        }
        return $this->table->generate();
    }

    /**
     * loadDisplay function
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param Mercurio45 $mercurio45
     * @return void
     */
    public function loadDisplay($mercurio45)
    {
        /* Tag::displayTo("tipdoc", $mercurio45->getTipdoc());
        Tag::displayTo("nit", $mercurio45->getNit());
        Tag::displayTo("cedtra", $mercurio45->getCedtra());
        Tag::displayTo("id", $mercurio45->getId());
        Tag::displayTo("telefono", $mercurio45->getTelefono());
        Tag::displayTo("celular", $mercurio45->getCelular());
        Tag::displayTo("email", $mercurio45->getEmail());
        Tag::displayTo("prinom", $mercurio45->getPrinom());
        Tag::displayTo("segnom", $mercurio45->getSegnom());
        Tag::displayTo("priape", $mercurio45->getPriape());
        Tag::displayTo("segape", $mercurio45->getSegape());
        Tag::displayTo("direccion", $mercurio45->getDireccion());
        Tag::displayTo("fecsol", $mercurio45->getFecsol()->getCurrentDate());
        Tag::displayTo("codciu", $mercurio45->getCodciu());
        Tag::displayTo("codzon", $mercurio45->getCodzon()); */
    }

    /**
     * rechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio45
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($mercurio45, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio45->getId();
        $mercurio45->setEstado('X');
        $mercurio45->setMotivo($nota);
        $mercurio45->setCodest($codest);
        $mercurio45->setFecest($today->format('Y-m-d'));
        $mercurio45->save();

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
     * @param [type] $mercurio45
     * @param [type] $nota
     * @param [type] $codest
     * @param string $campos_corregir
     * @return void
     */
    public function devolver($mercurio45, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio45->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio45->setEstado('D');
        $mercurio45->setMotivo($nota);
        $mercurio45->setCodest($codest);
        $mercurio45->setFecest($fecest);
        $mercurio45->save();

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
     * @param [type] $mercurio45
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio45, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por el trabajador: {$mercurio45->getPrinom()} {$mercurio45->getSegnom()} {$mercurio45->getPriape()} {$mercurio45->getSegape()} con identificación: {$mercurio45->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * msjRechazar function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio45
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio45, $nota)
    {
        return "La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, " .
            "emitida por el trabajador:  {$mercurio45->getPrinom()} {$mercurio45->getSegnom()} {$mercurio45->getPriape()} {$mercurio45->getSegape()} con identificación: {$mercurio45->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            "<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>" .
            "<br/>Gracias por preferirnos.";
    }

    /**
     * adjuntos function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio45
     * @return void
     */
    public function adjuntos($mercurio45)
    {
        $path = (new Mercurio01)->findFirst()->getPath();
        $adjuntos = '';
        $adjuntos .= "<div class='col-md-4 mb-2 shw-adjuntos'>";
        $adjuntos .= "<button class='btn-icon btn-block btn-outline-default' type='button' data-toggle='adjunto' data-path='{$path}' data-file='{$mercurio45->getArchivo()}' >";
        $adjuntos .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
        $adjuntos .= "<span class='btn-inner--text'>{$mercurio45->getNomcer()}</span>";
        $adjuntos .= "</button>";
        $adjuntos .= "</div>";
        return $adjuntos;
    }

    /**
     * seguimiento function
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $mercurio45
     * @return void
     */
    public function seguimiento($mercurio45)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio45);
    }
}
