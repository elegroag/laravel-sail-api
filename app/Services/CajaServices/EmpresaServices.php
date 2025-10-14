<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Services\Srequest;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class EmpresaServices
{
    private $orderpag = 'fecini';

    private $tipopc = 2;

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
        $this->controller_name = 'aprobacionemp';
        $this->registroSeguimiento = new RegistroSeguimiento;
    }

    /**
     * showTabla function
     *
     * @param  object  $paginate
     * @return string
     */
    public function showTabla($paginate)
    {
        $this->table->set_template($this->getTemplateTable());
        $this->table->set_heading(
            'OPT',
            'Días',
            'Recepción',
            'NIT',
            'Razón social',
            'Estado',
            'Fecha solicitud'
        );

        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $style = '#61b5ff';
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId(), $entity->getFecini());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) {
                        $style = '#d3a246; font-size:1.5em';
                    }
                    if ($dias_vencidos > 3) {
                        $style = '#ff6161; font-size:1.5em';
                    }
                } else {
                    $style = '#61b5ff';
                }
                $id = $entity->getId();
                // $sat = ($entity->getDocumentoRepresentanteSat() > 0) ? "SAT" : "NORMAL";
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span> ",
                    'NORMAL',
                    $entity->getNit(),
                    $entity->getRazsoc(),
                    $entity->getEstadoDetalle(),
                    $entity->getFeciniString()
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='7'> &nbsp; No hay registros que mostrar</td></tr>");
        }

        return $this->table->generate();
    }

    public function findPagination($query)
    {
        return Mercurio30::whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    /**
     * rechazar function
     *
     * @param  Mercurio30  $entity
     * @param [type] $nota
     * @param [type] $codest
     * @return void
     */
    public function rechazar($entity, $nota, $codest)
    {
        $today = Carbon::now();
        $id = $entity->getId();
        $entity->setEstado('X');
        $entity->setMotivo($nota);
        $entity->setCodest($codest);
        $entity->setFecest($today->format('Y-m-d'));
        $entity->save();

        $item = (new Mercurio10)->maximum('item', "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
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
     * @param  Mercurio30  $entity
     * @param  string  $nota
     * @param  string  $codest
     * @param  string  $campos_corregir
     * @return void
     */
    public function devolver($entity, $nota, $codest, $campos_corregir = '')
    {
        $today = Carbon::now();
        $id = $entity->getId();
        $fecest = $today->format('Y-m-d');
        $entity->setEstado('D');
        $entity->setMotivo($nota);
        $entity->setCodest($codest);
        $entity->setFecest($fecest);
        $entity->save();

        $item = (new Mercurio10)->maximum('item', "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;
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
        (new Mercurio10)->updateAll("campos_corregir='{$campos_corregir}'", "conditions: item='{$item}' AND numero='{$id}' AND tipopc='{$this->tipopc}'");

        return true;
    }

    /**
     * msjDevolver function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Mercurio30  $mercurio30
     * @param  string  $nota
     * @return void
     */
    public function msjDevolver($mercurio30, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por la empresa: {$mercurio30->getRazsoc()} con NIT: {$mercurio30->getNit()}.<br/>" .
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}" .
            '<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>' .
            '<br/>Gracias por preferirnos.';
    }

    /**
     * msjRechazar function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Mercurio30  $mercurio30
     * @param  string  $nota
     * @return void
     */
    public function msjRechazar($mercurio30, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por la empresa: {$mercurio30->getRazsoc()} con NIT: {$mercurio30->getNit()}.<br/>" .
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
     * @param [type] $mercurio30
     * @return void
     */
    public function adjuntos($mercurio30)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio30);
    }

    /**
     * seguimiento function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio30
     * @return void
     */
    public function seguimiento($mercurio30)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio30);
    }

    /**
     * dataOptional function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  array  $mercurio30
     * @return void
     */
    public function dataOptional($mercurio30, $estado = 'P')
    {
        $empresas = [];
        foreach ($mercurio30 as $ai => $mercurio) {
            $background = '';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mercurio->getId(), $mercurio->getFecini());
            if ($estado == 'P') {
                if ($dias_vencidos == 3) {
                    $background = '#f1f1ad';
                } elseif ($dias_vencidos > 3) {
                    $background = '#f5b2b2';
                }
            }

            if ($mercurio->getEstado() == 'A') {
                $url = env('APP_URL') . '/cajas/' . $this->controller_name . '/infoAprobadoView/' . $mercurio->getId();
            } else {
                $url = env('APP_URL') . '/cajas/' . $this->controller_name . '/info/' . $mercurio->getId();
            }

            $sat = 'NORMAL';
            if ($mercurio->getDocumentoRepresentanteSat() > 0) {
                $sat = 'SAT';
            }
            $empresas[] = [
                'estado' => $mercurio->getEstadoDetalle(),
                'recepcion' => $sat,
                'nit' => $mercurio->getNit(),
                'background' => $background,
                'razsoc' => $mercurio->getRazsoc(),
                'dias_vencidos' => $dias_vencidos,
                'id' => $mercurio->getId(),
                'url' => $url,
            ];
        }

        return $empresas;
    }

    public function findByUserAndEstado(Srequest $request)
    {
        $filtro = $request->getParam('filtro');
        $usuario = $request->getParam('usuario');
        $estado = $request->getParam('estado');

        $q = Mercurio30::where('usuario', $usuario)
            ->where('estado', $estado);
        if (! empty($filtro)) {
            $q->whereRaw($filtro);
        }
        $data = $q->get();

        $requests = [];
        foreach ($data as $row) {
            $style = '#61b5ff';
            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $row->getId(), Carbon::parse($row->getFeciniString()));
            if ($row->getEstado() == 'P') {
                if ($dias_vencidos == 3) {
                    $style = '#d3a246';
                }
                if ($dias_vencidos > 3) {
                    $style = '#ff6161';
                }
            } else {
                $style = '#61b5ff';
            }
            $method = ($row->getEstado() == 'A') ? 'infoAprobadoView' : 'info';
            $url = env('APP_URL') . '/cajas/' . $this->controller_name . '/' . $method . '/' . $row->getId();

            $sat = ($row->getDocumentoRepresentanteSat() > 0) ? 'SAT' : 'NORMAL';

            // <i class='fas fa-bell fa-2x' style='color:{$style}'> {$dias_vencidos} </i>,
            // <a href='{$url}' class='btn btn-xs btn-primary' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>
            $requests[] = [
                'url' => $url,
                'style' => $style,
                'dias' => $dias_vencidos,
                'sat' => $sat,
                'nit' => $row->getNit(),
                'razsoc' => $row->getRazsoc(),
                'estado' => $this->estadoDetalleFromCode($row->getEstado()),
                'fecini' => $row->getFeciniString(),
            ];
        }

        return $requests;
    }

    private function estadoDetalleFromCode($code)
    {
        switch ($code) {
            case 'T':
                return 'TEMPORAL';
            case 'D':
                return 'DEVUELTO';
            case 'A':
                return 'APROBADO';
            case 'X':
                return 'RECHAZADO';
            case 'P':
                return 'PENDIENTE';
            default:
                return '';
        }
    }
}
