<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio47;
use App\Services\Tag;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\RegistroSeguimiento;
use App\Services\Utils\Table;
use Carbon\Carbon;
use Exception;

class UpDatosTrabajadorService
{
    private $orderpag = 'fecha_solicitud';

    private $tipopc = '14';

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
            'Cedtra',
            'Nombre',
            'Estado',
            'Fecha solicitud'
        );

        if ($paginate->items) {
            foreach ($paginate->items as $entity) {
                $style = '#61b5ff';
                $dias_vencidos = CalculatorDias::calcular($this->tipopc, $entity->getId(), $entity->getFechaSolicitud());
                if ($entity->getEstado() == 'P') {
                    if ($dias_vencidos == 3) {
                        $style = '#d3a246; font-size:1.3em';
                    }
                    if ($dias_vencidos > 3) {
                        $style = '#ff6161; font-size:1.3em';
                    }
                } else {
                    $style = '#61b5ff';
                }

                $id = $entity->getId();
                $this->table->add_row(
                    "<a data-cid='{$id}' data-toggle='info' class='btn btn-xs btn-primary text-white' title='Info'> <i class='fas fa-hand-point-up text-white'></i></a>",
                    " <i class='fas fa-bell' style='color:{$style}'></i> <span class='text-nowrap'>{$dias_vencidos}</span> ",
                    $entity->getDocumento(),
                    '',
                    '',
                    $entity->getFechaSolicitud()
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
        return Mercurio47::where('tipo_actualizacion', 'T')->whereRaw($query)->orderBy($this->orderpag, 'DESC')->get();
    }

    public function getTemplateTable()
    {
        return Table::TmpGeneral();
    }

    /**
     * loadDisplay function
     *
     * @param  Mercurio47  $mercurio47
     * @return void
     */
    public function loadDisplay($mercurio47)
    {
        Tag::displayTo('tipdoc', $mercurio47->getTipdoc());
        Tag::displayTo('nit', $mercurio47->getNit());
        Tag::displayTo('id', $mercurio47->getId());
        Tag::displayTo('sigla', $mercurio47->getSigla());
        Tag::displayTo('calemp', $mercurio47->getCalemp());
        Tag::displayTo('cedrep', $mercurio47->getCedrep());
        Tag::displayTo('repleg', $mercurio47->getRepleg());
        Tag::displayTo('telefono', $mercurio47->getTelefono());
        Tag::displayTo('celular', $mercurio47->getCelular());
        Tag::displayTo('fax', $mercurio47->getFax());
        Tag::displayTo('email', $mercurio47->getEmail());
        Tag::displayTo('tottra', $mercurio47->getTottra());
        Tag::displayTo('valnom', $mercurio47->getValnom());
        Tag::displayTo('dirpri', $mercurio47->getDirpri());
        Tag::displayTo('ciupri', $mercurio47->getCiupri());
        Tag::displayTo('celpri', $mercurio47->getCelpri());
        Tag::displayTo('emailpri', $mercurio47->getEmailpri());
        Tag::displayTo('prinom', $mercurio47->getPrinom());
        Tag::displayTo('segnom', $mercurio47->getSegnom());
        Tag::displayTo('priape', $mercurio47->getPriape());
        Tag::displayTo('segape', $mercurio47->getSegape());
        Tag::displayTo('priaperepleg', $mercurio47->getPriaperepleg());
        Tag::displayTo('segnomrepleg', $mercurio47->getSegnomrepleg());
        Tag::displayTo('prinomrepleg', $mercurio47->getPrinomrepleg());
        Tag::displayTo('segaperepleg', $mercurio47->getSegaperepleg());
        Tag::displayTo('razsoc', $mercurio47->getRazsoc());
        Tag::displayTo('tipper', $mercurio47->getTipper());
        Tag::displayTo('direccion', $mercurio47->getDireccion());
        Tag::displayTo('tipsoc', $mercurio47->getTipsoc());
        Tag::displayTo('codact', $mercurio47->getCodact());
        Tag::displayTo('fecini', $mercurio47->getFechaSolicitud());
        Tag::displayTo('digver', $mercurio47->getDigver());
        Tag::displayTo('codciu', $mercurio47->getCodciu());
        Tag::displayTo('codzon', $mercurio47->getCodzon());
        Tag::displayTo('coddocrepleg', $mercurio47->getCoddocrepleg());
        Tag::displayTo('matmer', $mercurio47->getMatmer());
        Tag::displayTo('telpri', $mercurio47->getTelpri());
        Tag::displayTo('tipemp', $mercurio47->getTipemp());
        Tag::displayTo('pymes', 'N');
        Tag::displayTo('forpre', 'U');
        Tag::displayTo('ofiafi', '01');
        Tag::displayTo('subpla', '001');
        Tag::displayTo('feccap', date('Y-m-d'));
        Tag::displayTo('contratista', 'N');
        Tag::displayTo('codind', '03');
        Tag::displayTo('colegio', 'N');
    }

    /**
     * rechazar function
     *
     * @param  Mercurio47  $entity
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
     * @param  Mercurio47  $entity
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
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Mercurio47  $mercurio47
     * @param  string  $nota
     * @return void
     */
    public function msjDevolver($mercurio47, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por la empresa: {$mercurio47->getRazsoc()} con NIT: {$mercurio47->getNit()}.<br/>" .
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
     * @param  Mercurio47  $mercurio47
     * @param  string  $nota
     * @return void
     */
    public function msjRechazar($mercurio47, $nota)
    {
        return 'La Caja de Compensación Familiar Comfaca, ha recepcionado y validado la solicitud de afiliación, ' .
            "emitida por la empresa: {$mercurio47->getRazsoc()} con NIT: {$mercurio47->getNit()}.<br/>" .
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
     * @param [type] $mercurio47
     * @return void
     */
    public function adjuntos($mercurio47)
    {
        return $this->registroSeguimiento->loadAdjuntos($this->tipopc, $mercurio47);
    }

    /**
     * seguimiento function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $mercurio47
     * @return void
     */
    public function seguimiento($mercurio47)
    {
        return $this->registroSeguimiento->consultaSeguimiento($this->tipopc, $mercurio47);
    }
}
