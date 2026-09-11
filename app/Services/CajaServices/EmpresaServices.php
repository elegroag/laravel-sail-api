<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Services\Srequest;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\Mercurio10Cierre;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class EmpresaServices
{
    private string $orderpag = 'fecini';

    private string $tipopc = '2';

    private string $controller_name;

    private RegistroSeguimiento $registroSeguimiento;

    private Table $table;

    public function __construct()
    {

        $this->table = new Table;
        $this->controller_name = 'aprobacionemp';
        $this->registroSeguimiento = new RegistroSeguimiento;
    }

    /**
     * showTabla function
     */
    public function showTabla(object $paginate): string
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
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->id, $entity->fecsol);
                if ($entity->estado == 'P') {
                    if ($dias_vencidos == 3) {
                        $style = '#d3a246; font-size:1.5em';
                    }
                    if ($dias_vencidos > 3) {
                        $style = '#ff6161; font-size:1.5em';
                    }
                } else {
                    $style = '#61b5ff';
                }
                $id = $entity->id;
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span> ",
                    'NORMAL',
                    $entity->nit,
                    $entity->razsoc,
                    estado_detalle_value($entity->estado),
                    $entity->fecsol
                );
            }
        } else {
            $this->table->add_row('');
            $this->table->set_empty("<tr><td colspan='7'> &nbsp; No hay registros que mostrar</td></tr>");
        }

        return $this->table->generate();
    }

    public function findPagination(string $query): mixed
    {
        return Mercurio30::whereRaw($query)->orderBy($this->orderpag, 'asc')->get();
    }

    public function getTemplateTable(): array
    {
        return Table::TmpGeneral();
    }

    public function rechazar(Mercurio30 $entity, string $nota, string $codest): bool
    {
        $today = Carbon::now();
        $id = $entity->getId();
        $entity->setEstado('X');
        $entity->setMotivo($nota);
        $entity->setCodest($codest);
        $entity->setFecest($today->format('Y-m-d'));
        $entity->save();

        $item = (int) Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->max('item') + 1;
        $mercurio10 = Mercurio10::create([
            'tipopc' => $this->tipopc,
            'numero' => $id,
            'item' => $item,
            'estado' => 'X',
            'nota' => $nota,
            'codest' => $codest,
            'fecsis' => $today->format('Y-m-d'),
        ]);

        Mercurio10Cierre::aplicarCierreRespuesta($mercurio10);

        return true;
    }

    /**
     * devolver function
     */
    public function devolver(
        Mercurio30 $entity,
        string $nota,
        string $codest,
        ?string $campos_corregir = null
    ): bool {
        $today = Carbon::now();
        $id = $entity->getId();
        $fecest = $today->format('Y-m-d');
        $entity->setEstado('D');
        $entity->setMotivo($nota);
        $entity->setCodest($codest);
        $entity->setFecest($fecest);
        $entity->save();

        $item = (int) Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->max('item') + 1;
        $mercurio10 = Mercurio10::create([
            'tipopc' => $this->tipopc,
            'numero' => $id,
            'item' => $item,
            'estado' => 'D',
            'nota' => $nota,
            'codest' => $codest,
            'fecsis' => $today->format('Y-m-d'),
            'campos_corregir' => $campos_corregir,
        ]);

        Mercurio10Cierre::aplicarCierreRespuesta($mercurio10);

        DevolucionNotificacion::notificar(
            $entity,
            $nota,
            'Solicitud de afiliación de empresa devolución'
        );

        return true;
    }

    /**
     * msjDevolver function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     */
    public function msjDevolver(Mercurio30 $mercurio30, string $nota): string
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, '.
            "emitida por la empresa: {$mercurio30->getRazsoc()} con NIT: {$mercurio30->getNit()}.<br/>".
            "E informamos que su solicitud fue devuelta por el siguiente motivo:<br/> {$nota}".
            '<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>'.
            '<br/>Gracias por preferirnos.';
    }

    /**
     * msjRechazar function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     */
    public function msjRechazar(Mercurio30 $mercurio30, string $nota): string
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, '.
            "emitida por la empresa: {$mercurio30->getRazsoc()} con NIT: {$mercurio30->getNit()}.<br/>".
            "E informamos que su solicitud fue rechazada por el siguiente motivo:<br/> {$nota}".
            '<p>En caso de requerir el acompañamiento de algún asesor técnico para hacer la actualización, puede comunicarse a la línea de atención 4366300,1066.</p>'.
            '<br/>Gracias por preferirnos.';
    }

    /**
     * adjuntos function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     */
    public function adjuntos(Mercurio30 $mercurio30): mixed
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio30);
    }

    /**
     * seguimiento function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     */
    public function seguimiento(Mercurio30 $mercurio30): mixed
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio30);
    }

    /**
     * dataOptional function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     */
    public function dataOptional(
        Mercurio30 $mercurio30,
        string $estado = 'P'
    ): ?array {
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
                $url = config('app.url').'/cajas/'.$this->controller_name.'/infoAprobadoView/'.$mercurio->getId();
            } else {
                $url = config('app.url').'/cajas/'.$this->controller_name.'/info/'.$mercurio->getId();
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

        return $empresas ?? null;
    }

    public function findByUserAndEstado(Srequest $request): ?array
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
            $url = config('app.url').'/cajas/'.$this->controller_name.'/'.$method.'/'.$row->getId();

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

    private function estadoDetalleFromCode(string $code): string
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
