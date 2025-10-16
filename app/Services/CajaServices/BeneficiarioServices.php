<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio34;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class BeneficiarioServices
{
    private $orderpag = 'fecsol';

    private $tipopc = 4;

    private $controller_name;

    /**
     * registroSeguimiento variable
     *
     * @var RegistroSeguimiento
     */
    private $registroSeguimiento;

    /**
     * table variable
     *
     * @var Table
     */
    private $table;

    public function __construct()
    {
        $this->table = new Table;
        $this->controller_name = 'aprobacionben';
        $this->registroSeguimiento = new RegistroSeguimiento;
    }

    /**
     * findPagination function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $query
     * @return void
     */
    public function findPagination($query)
    {
        return Mercurio34::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    /**
     * showTabla function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  object  $paginate
     * @return void
     */
    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            'OPT',
            'Días',
            'Identificación',
            'Nombre beneficiario',
            'Cedula trabajador',
            'Estado',
            'Fecha solicitud'
        );

        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $style = '#61b5ff';
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId(), $entity->getFecsol());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) {
                        $style = '#d3a246; font-size:1.3em';
                    }
                    if ($dias_vencidos > 3) {
                        $style = '#ff6161; font-size:1.3em';
                    }
                } else {
                    $style = '#344767';
                }
                if ($dias_vencidos == 0) {
                    $dias_vencidos = '';
                }
                $id = $entity->getId();
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span> ",
                    $entity->getNumdoc(),
                    $entity->getPrinom() . ' ' . $entity->getSegnom() . ' ' . $entity->getPriape() . ' ' . $entity->getSegape(),
                    $entity->getCedtra(),
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
     *
     * @changed [2023-12-19]

     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    /**
     * loadDisplay function
     *
     * @changed [2023-12-19]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Mercurio34  $mercurio34
     * @return void
     */
    public function loadDisplay($mercurio34)
    {
        /* Tag::displayTo("tipdoc", $mercurio34->getTipdoc());
        Tag::displayTo("cedtra", $mercurio34->getCedtra());
        Tag::displayTo("cedcon", $mercurio34->getCedcon());
        Tag::displayTo("id", $mercurio34->getId());
        Tag::displayTo("telefono", $mercurio34->getTelefono());
        Tag::displayTo("celular", $mercurio34->getCelular());
        Tag::displayTo("email", $mercurio34->getEmail());
        Tag::displayTo("prinom", $mercurio34->getPrinom());
        Tag::displayTo("segnom", $mercurio34->getSegnom());
        Tag::displayTo("priape", $mercurio34->getPriape());
        Tag::displayTo("segape", $mercurio34->getSegape());
        Tag::displayTo("direccion", $mercurio34->getDireccion());
        Tag::displayTo("fecsol", $mercurio34->getFecsol()->getCurrentDate());
        Tag::displayTo("codciu", $mercurio34->getCodciu());
        Tag::displayTo("codzon", $mercurio34->getCodzon()); */
    }

    /**
     * rechazar function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($mercurio34, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $mercurio34->getId();
        $mercurio34->setEstado('X');
        $mercurio34->setMotivo($nota);
        $mercurio34->setCodest($codest);
        $mercurio34->setFecest($today->format('Y-m-d'));
        $mercurio34->save();

        $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;
        $mercurio10 = new Mercurio10;
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem($item);
        $mercurio10->setEstado('X');
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d'));

        if (! $mercurio10->save()) {
            $msj = '';
            foreach ($mercurio10->getMessages() as $key => $mess) {
                $msj .= $mess->getMessage() . '<br/>';
            }
            throw new DebugException('Error ' . $msj, 501);
        }

        return true;
    }

    /**
     * devolver function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @param [type] $nota
     * @param [type] $codest
     * @param  string  $campos_corregir
     * @return void
     */
    public function devolver($mercurio34, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $mercurio34->getId();
        $fecest = $today->format('Y-m-d');
        $mercurio34->setEstado('D');
        $mercurio34->setMotivo($nota);
        $mercurio34->setCodest($codest);
        $mercurio34->setFecest($fecest);
        $mercurio34->save();

        $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;
        $mercurio10 = new Mercurio10;
        $mercurio10->setTipopc($this->tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem($item);
        $mercurio10->setEstado('D');
        $mercurio10->setNota($nota);
        $mercurio10->setCodest($codest);
        $mercurio10->setFecsis($today->format('Y-m-d'));

        if (! $mercurio10->save()) {
            $msj = '';
            foreach ($mercurio10->getMessages() as $key => $message) {
                $msj .= $message . '<br/>';
            }
            throw new Exception('Error ' . $msj, 501);
        }
        Mercurio10::whereRaw("item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'")->update(['campos_corregir' => $campos_corregir]);
        return true;
    }

    /**
     * msjDevolver function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @param [type] $nota
     * @return void
     */
    public function msjDevolver($mercurio34, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por el trabajador: {$mercurio34->getPrinom()} {$mercurio34->getSegnom()} {$mercurio34->getPriape()} {$mercurio34->getSegape()} con identificación: {$mercurio34->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            '<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>' .
            '<br/>Gracias por preferirnos.';
    }

    /**
     * msjRechazar function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @param [type] $nota
     * @return void
     */
    public function msjRechazar($mercurio34, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por el trabajador:  {$mercurio34->getPrinom()} {$mercurio34->getSegnom()} {$mercurio34->getPriape()} {$mercurio34->getSegape()} con identificación: {$mercurio34->getCedtra()}.<br/>" .
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}" .
            '<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>' .
            '<br/>Gracias por preferirnos.';
    }

    /**
     * adjuntos function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @return void
     */
    public function adjuntos($mercurio34)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio34);
    }

    /**
     * seguimiento function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio34
     * @return void
     */
    public function seguimiento($mercurio34)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio34);
    }

    /**
     * dataOptional function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  array  $mercurio34
     * @return void
     */
    public function dataOptional($mercurio34, $estado = 'P')
    {
        $beneficiarios = [];
        foreach ($mercurio34 as $ai => $mercurio) {
            $background = '';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecsol());
            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } elseif ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }

            $method = ($mercurio->getEstado() == 'A') ? 'infoAprobadoView' : 'info';
            $url = base_path() . '/' . config('app.url') . '/' . $this->controller_name . '/' . $method . '/' . $mercurio->getId();

            $sat = 'NORMAL';
            $beneficiarios[] = [
                'estado' => $mercurio->getEstadoDetalle(),
                'recepcion' => $sat,
                'numdoc' => $mercurio->getNumdoc(),
                'cedtra' => $mercurio->getCedtra(),
                'cedcon' => $mercurio->getCedcon(),
                'nit' => $mercurio->getNit(),
                'prinom' => $mercurio->getPrinom(),
                'segnom' => $mercurio->getSegnom(),
                'priape' => $mercurio->getPriape(),
                'segape' => $mercurio->getSegape(),
                'background' => $background,
                'dias_vencidos' => $dias_vencidos,
                'id' => $mercurio->getId(),
                'fecsol' => $mercurio->getFecsol(),
                'url' => $url,
            ];
        }

        return $beneficiarios;
    }
}
