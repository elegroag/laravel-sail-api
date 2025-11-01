<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio37;
use App\Models\Mercurio40;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Logger;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DomesticoController extends ApplicationController
{
    private $tipopc = '12';

    private $query = '1=1';

    private $cantidad_pagina = 0;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function aplicarFiltro(Request $request)
    {
        return $this->buscar($request);
    }

    public function showTabla($paginate)
    {
        $html = "<table class='table align-items-center table-flush'>";
        $html .= "<thead class='thead-light'>";
        $html .= '<tr>';
        $html .= "<th scope='col'>Documento</th>";
        $html .= "<th scope='col'>Nombre</th>";
        $html .= "<th scope='col'>Estado</th>";
        $html .= "<th scope='col'></th>";
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {
            $html .= '<tr>';
            $html .= "<td>{$mtable->getCedtra()}</td>";
            $html .= "<td>{$mtable->getPriape()} {$mtable->getSegape()} {$mtable->getPrinom()} {$mtable->getSegnom()}</td>";
            $html .= "<td>{$mtable->getEstado()}</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action' data-toggle='tooltip' data-original-title='Editar' onclick='editar(\"{$mtable->getId()}\")'>";
            $html .= "<i class='fas fa-user-edit'></i>";
            $html .= '</a>';
            $html .= "<a href='#!' class='table-action ' data-toggle='tooltip' data-original-title='Info' onclick='info(\"{$mtable->getId()}\")'>";
            $html .= "<i class='fas fa-clipboard-check'></i>";
            $html .= '</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    public function changeCantidadPagina(Request $request)
    {
        $this->setResponse('ajax');
        $this->cantidad_pagina = $request->input('numero');
        self::buscar($request);
    }

    public function index()
    {
        $help = 'Esta opcion permite manejar los ';

        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'PoblacionAfilia',
                'metodo' => 'captura_trabajador',
            ]
        );
        $datos_captura = $ps->toArray();
        if ($datos_captura['success'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = [];
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_sexo = [];
            foreach ($datos_captura['sexo'] as $data) {
                $_sexo[$data['sexo']] = $data['detalle'];
            }
            $_estciv = [];
            foreach ($datos_captura['estciv'] as $data) {
                $_estciv[$data['estciv']] = $data['detalle'];
            }
            $_cabhog = [];
            foreach ($datos_captura['cabhog'] as $data) {
                $_cabhog[$data['cabhog']] = $data['detalle'];
            }
            $_codciu = [];
            foreach ($datos_captura['codciu'] as $data) {
                $_codciu[$data['codciu']] = $data['detalle'];
            }
            $_codzon = [];
            foreach ($datos_captura['codzon'] as $data) {
                $_codzon[$data['codzon']] = $data['detalle'];
            }
            $_captra = [];
            foreach ($datos_captura['captra'] as $data) {
                $_captra[$data['captra']] = $data['detalle'];
            }
            $_tipdis = [];
            foreach ($datos_captura['tipdis'] as $data) {
                $_tipdis[$data['tipdis']] = $data['detalle'];
            }
            $_nivedu = [];
            foreach ($datos_captura['nivedu'] as $data) {
                $_nivedu[$data['nivedu']] = $data['detalle'];
            }
            $_rural = [];
            foreach ($datos_captura['rural'] as $data) {
                $_rural[$data['rural']] = $data['detalle'];
            }
            $_vivienda = [];
            foreach ($datos_captura['vivienda'] as $data) {
                $_vivienda[$data['vivienda']] = $data['detalle'];
            }
            $_tipafi = [];
            foreach ($datos_captura['tipafi'] as $data) {
                $_tipafi[$data['tipafi']] = $data['detalle'];
            }
        }

        return view('mercurio.domestico.index', [
            'help' => $help,
            'title' => 'Afiliacion Domestico',
            'buttons' => ['N'],
            '_coddoc' => $_coddoc,
            '_sexo' => $_sexo,
            '_estciv' => $_estciv,
            '_cabhog' => $_cabhog,
            '_codciu' => $_codciu,
            '_codzon' => $_codzon,
            '_captra' => $_captra,
            '_tipdis' => $_tipdis,
            '_nivedu' => $_nivedu,
            '_rural' => $_rural,
            '_vivienda' => $_vivienda,
            '_tipafi' => $_tipafi,
            'calemp' => 'S',
            'codact' => '201010',
            'cedtra' => parent::getActUser('documento'),
            'coddoc' => parent::getActUser('coddoc'),
            'calemp' => 'S',
            'codact' => '201010',
            'tipdoc' => parent::getActUser('coddoc'),
        ]);
    }

    public function buscar(Request $request) {}

    public function editar(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id');
            $mercurio40 = Mercurio40::where('id', $id)->first();
            if ($mercurio40 == false) {
                $mercurio40 = new Mercurio40;
            }

            $response = $mercurio40->getArray();
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
        $this->renderObject($response);
    }

    public function guardar(Request $request)
    {
        try {
            $generalService = new GeneralService;
            $this->setResponse('ajax');
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $cedtra = $request->input('cedtra', 'addslaches', 'extraspaces', 'striptags');
            $tipdoc = $request->input('tipdoc', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $priape = $request->input('priape', 'addslaches', 'extraspaces', 'striptags');
            $segape = $request->input('segape', 'addslaches', 'extraspaces', 'striptags');
            $prinom = $request->input('prinom', 'addslaches', 'extraspaces', 'striptags');
            $segnom = $request->input('segnom', 'addslaches', 'extraspaces', 'striptags');
            $fecnac = $request->input('fecnac', 'addslaches', 'extraspaces', 'striptags');
            $ciunac = $request->input('ciunac', 'addslaches', 'extraspaces', 'striptags');
            $sexo = $request->input('sexo', 'addslaches', 'extraspaces', 'striptags');
            $estciv = $request->input('estciv', 'addslaches', 'extraspaces', 'striptags');
            $cabhog = $request->input('cabhog', 'addslaches', 'extraspaces', 'striptags');
            $codciu = $request->input('codciu', 'addslaches', 'extraspaces', 'striptags');
            $codzon = $request->input('codzon', 'addslaches', 'extraspaces', 'striptags');
            $direccion = $request->input('direccion', 'addslaches', 'extraspaces', 'striptags');
            $barrio = $request->input('barrio', 'addslaches', 'extraspaces', 'striptags');
            $telefono = $request->input('telefono', 'addslaches', 'extraspaces', 'striptags');
            $celular = $request->input('celular', 'addslaches', 'extraspaces', 'striptags');
            $fax = $request->input('fax', 'addslaches', 'extraspaces', 'striptags');
            $email = $request->input('email', 'addslaches', 'extraspaces', 'striptags');
            $fecing = $request->input('fecing', 'addslaches', 'extraspaces', 'striptags');
            $salario = $request->input('salario', 'addslaches', 'extraspaces', 'striptags');
            $captra = $request->input('captra', 'addslaches', 'extraspaces', 'striptags');
            $tipdis = $request->input('tipdis', 'addslaches', 'extraspaces', 'striptags');
            $nivedu = $request->input('nivedu', 'addslaches', 'extraspaces', 'striptags');
            $rural = $request->input('rural', 'addslaches', 'extraspaces', 'striptags');
            $vivienda = $request->input('vivienda', 'addslaches', 'extraspaces', 'striptags');
            $tipafi = $request->input('tipafi', 'addslaches', 'extraspaces', 'striptags');
            $autoriza = $request->input('autoriza', 'addslaches', 'extraspaces', 'striptags');
            $calemp = $request->input('calemp', 'addslaches', 'extraspaces', 'striptags');
            $codact = $request->input('codact', 'addslaches', 'extraspaces', 'striptags');
            $modelos = ['Mercurio20', 'Mercurio40'];
            // $Transaccion = parent::startTrans($modelos);
            // $response = parent::startFunc();

            $logger = new Logger;
            $id_log = $logger->registrarLog(true, 'Afiliacion Trabajador', '');
            if ($id == '') {
                $mercurio40 = new Mercurio40;
                $mercurio40->setId(0);
                $mercurio40->setLog($id_log);
            } else {
                $mercurio40 = Mercurio40::where('id', $id)->first();
            }
            // $mercurio40->setTransaction($Transaccion);
            $mercurio40->setCedtra($cedtra);
            $mercurio40->setTipdoc($tipdoc);
            $mercurio40->setPriape($priape);
            $mercurio40->setSegape($segape);
            $mercurio40->setPrinom($prinom);
            $mercurio40->setSegnom($segnom);
            $mercurio40->setFecnac($fecnac);
            $mercurio40->setCiunac($ciunac);
            $mercurio40->setSexo($sexo);
            $mercurio40->setEstciv($estciv);
            $mercurio40->setCabhog($cabhog);
            $mercurio40->setCodciu($codciu);
            $mercurio40->setCodzon($codzon);
            $mercurio40->setDireccion($direccion);
            $mercurio40->setBarrio($barrio);
            $mercurio40->setTelefono($telefono);
            $mercurio40->setCelular($celular);
            $mercurio40->setFax($fax);
            $mercurio40->setEmail($email);
            $mercurio40->setFecing($fecing);
            $mercurio40->setSalario($salario);
            $mercurio40->setCaptra($captra);
            $mercurio40->setTipdis($tipdis);
            $mercurio40->setNivedu($nivedu);
            $mercurio40->setRural($rural);
            $mercurio40->setVivienda($vivienda);
            $mercurio40->setTipafi($tipafi);
            $mercurio40->setAutoriza($autoriza);
            $mercurio40->setCalemp($calemp);
            $mercurio40->setCodact($codact);
            $mercurio40->setEstado('T');

            $asignarFuncionario = new AsignarFuncionario;

            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            if ($usuario == '') {
                $response = 'No se puede realizar el registro,Comuniquese con la Atencion al cliente';

                return $this->renderObject($response);
            }

            $mercurio40->setUsuario($usuario);
            $mercurio40->setTipo(parent::getActUser('tipo'));
            $mercurio40->setCoddoc(parent::getActUser('coddoc'));
            $mercurio40->setDocumento(parent::getActUser('documento'));
            $mercurio40->save();

            // parent::finishTrans();
            $response = 'Creacion Con Exito';

            return $this->renderText(json_encode($response));
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function validePk(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $cedtra = $request->input('cedtra');
            $l = Mercurio40::where('cedtra', $cedtra)
                ->whereIn('estado', ['T', 'A', 'P'])
                ->count();

            if ($l > 0) {
                throw new DebugException('La Conyuge ya se encuentra', 1);
            }

            $response = [
                'success' => true,
                'msj' => 'Proceso completado con éxito.',
            ];
        } catch (DebugException $e) {
            $response = [
                'succcess' => true,
                'error' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function infor(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');
        $mercurio40 = Mercurio40::where('id', $id)->first();
        $response = '';
        // $response .= parent::consultaDomestico($mercurio40);
        $response .= "<hr class='my-3'>";
        $response .= "<button type='button' class='btn btn-primary btn-lg btn-block' onclick='descargar_formulario($id);'>Descargar Formulario</button>";
        $response .= "<hr class='my-3'>";
        $response .= "<div class='row'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= '<thead>';
        $response .= '<tr>';
        $response .= '<th colspan=3>Archivos a adjuntar</th>';
        $response .= '</tr>';
        $response .= '</thead>';
        $response .= '<tbody>';

        $mercurio01 = Mercurio01::first();
        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->get();

        foreach ($mercurio13 as $mmercurio13) {

            $mercurio12 = Mercurio12::where('coddoc', $mmercurio13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $mercurio40->getId())
                ->where('coddoc', $mmercurio13->getCoddoc())
                ->first();

            if ($mercurio37 == false) {
                $obliga = '';
                if ($mmercurio13->getObliga() == 'S') {
                    $obliga = "<br><small class='text-muted'>Obligatorio</small>";
                }
                $response .= '<tr>';
                $response .= "<td>{$mercurio12->getDetalle()} $obliga</td>";
                $response .= '<td>';
                $response .= "<div class='custom-file'>";
                $response .= "<input type='file' class='custom-file-input' id='archivo_{$mmercurio13->getCoddoc()}' name='archivo_{$mmercurio13->getCoddoc()}' accept='application/pdf, image/*'>";
                $response .= "<label class='custom-file-label' for='customFileLang'>Select file</label>";
                $response .= '</div>';
                $response .= '</td>';
                $response .= '<td>';
                $response .= "<button class='btn btn-icon btn-primary btn-sm' type='button' onclick=\"guardarArchivo('$id','{$mmercurio13->getCoddoc()}')\"> <span class='btn-inner--icon'><i class='fas fa-plus'></i></span> </button>";
                $response .= '</td>';
                $response .= '</tr>';
            }
        }
        $response .= '</tbody>';
        $response .= '</table>';
        $response .= '</div>';

        $response .= "<hr class='my-3'>";

        $response .= "<div class='row'>";
        $response .= "<div class='col-11 m-auto'>";
        $response .= "<div class='jumbotron'>";
        $response .= '<h2>Enviar Radicado</h2>';
        $response .= '<p>Esta opcion envia la radicacion a la caja para su verificacion</p>';
        $response .= "<hr class='my-3'>";
        $response .= "<button type='button' class='btn btn-success btn-block' onclick='enviarCaja($id)'>Enviar Caja</button>";
        $response .= '</div>';
        $response .= '</div>';
        $response .= '</div>';

        return $this->renderText(json_encode($response));
    }

    public function borrarArchivo(Request $request)
    {
        try {

            $this->setResponse('ajax');
            $numero = $request->input('numero', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $coddoc = $request->input('coddoc', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $modelos = ['mercurio37'];

            $mercurio01 = Mercurio01::first();
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->first();

            unlink($mercurio01->getPath() . $mercurio37->getArchivo());

            Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->delete();

            $response = [
                'success' => true,
                'msj' => 'Se borro con Exito el archivo',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    public function guardarArchivo(Request $request)
    {
        try {

            $this->setResponse('ajax');
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $mercurio01 = Mercurio01::first();

            $mercurio37 = new Mercurio37;
            $mercurio37->setTipopc($this->tipopc);
            $mercurio37->setNumero($id);
            $mercurio37->setCoddoc($coddoc);
            $time = strtotime('now');

            if (isset($_FILES['archivo_' . $coddoc]['name']) && $_FILES['archivo_' . $coddoc]['name'] != '') {
                $extension = explode('.', $_FILES['archivo_' . $coddoc]['name']);
                $name = $this->tipopc . '_' . $id . "_{$coddoc}_{$time}." . end($extension);
                $_FILES['archivo_' . $coddoc]['name'] = $name;
                // $estado = $this->uploadFile("archivo_" . $coddoc, $mercurio01->getPath());
                /* if ($estado != false) {

                        $mercurio37->setArchivo($name);
                        $mercurio37->save();
                        $response = "Se adjunto con exito el archivo";
                    } else {
                        throw new DebugException("No se cargo: Tamano del archivo muy grande o No es Valido");
                    } */
            } else {
                $response = 'No se cargo el archivo';
            }

            $response = [
                'success' => true,
                'msj' => $response,
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
                'msj' => 'No se puede guardar/editar el Registro',
            ];
        }

        return $this->renderText($response);
    }

    public function enviarCaja(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $today = Carbon::now();

            if ((new Mercurio37)->getCount(
                '*',
                "conditions: tipopc='$this->tipopc' and numero='$id' and coddoc in (select coddoc from mercurio13 where tipopc='{$this->tipopc}' and obliga='S')"
            ) < (new Mercurio13)->getCount(
                '*',
                "conditions: tipopc='$this->tipopc' and obliga='S'"
            )) {
                $response = 'Adjunte los archivos obligatorios';

                return $this->renderText(json_encode($response));
            }

            Mercurio40::where('id', $id)->update(['estado' => 'P']);
            $item = Mercurio10::where('tipopc', $this->tipopc)
                ->where('numero', $id)
                ->max('item') + 1;

            $mercurio10 = new Mercurio10;
            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado('P');
            $mercurio10->setNota('Envio a la Caja para Verificacion');
            $mercurio10->setFecsis($today->format('Y-m-d'));
            $mercurio10->save();

            $response = 'Se envio con Exito';
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }
}
