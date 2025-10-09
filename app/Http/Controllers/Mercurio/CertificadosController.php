<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio10;
use App\Models\Mercurio45;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\Logger;
use App\Services\Utils\UploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificadosController extends ApplicationController
{
    protected $tipopc = '8';

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        $ps = Comman::Api();
        $ps->runCli(
            [
                'servicio' => 'Certificados',
                'metodo' => 'buscarCertificadosBeneficiario',
                'params' => $this->user['documento'],
            ]
        );

        $beneficiarios = false;
        $certificadosPresentados = false;
        $out = $ps->toArray();

        if ($out['success']) {
            $beneficiarios = $out['data'];
            $certificadosPresentados = [];
            foreach ($beneficiarios as $ai => $beneficiario) {
                $has = Mercurio45::where('codben', $beneficiario['codben'])->where('estado', 'P')->count();
                if ($has) {
                    $certificadosPresentados = $has;
                    $beneficiarios[$ai]['certificadoPendiente'] = true;
                    $beneficiarios[$ai]['certificados'] = $has;
                }
            }
        }

        return view(
            'mercurio/certificados/index',
            [
                'certificadosPresentados' => $certificadosPresentados,
                'subsi22' => $beneficiarios,
                'title' => 'Presentación Certificados',
            ]
        );
    }

    public function guardarAction(Request $request)
    {
        $message = '';
        $this->db->begin();
        try {
            $id = $request->input('id');
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $codben = $request->input('codben');
            $nombre = $request->input('nombre');
            $codcer = $request->input('codcer');
            $nomcer = $request->input('nomcer');

            if ((new Mercurio45)->getCount(
                '*',
                "conditions: codben='$codben' and codcer='$codcer' and estado <> 'X'"
            ) > 0) {
                $message = 'Ya tiene un certificado presentando, por favor espere a su aprobacion';

                return $this->renderObject($message);
            }

            $today = Carbon::now();
            $logger = new Logger;
            $id_log = $logger->registrarLog(true, 'Presentacion Certificados', '');
            $mercurio45 = new Mercurio45;
            $mercurio45->setLog($id_log);
            $mercurio45->setCedtra($documento);
            $mercurio45->setCodben($codben);
            $mercurio45->setNombre($nombre);
            $mercurio45->setCodcer($codcer);
            $mercurio45->setNomcer($nomcer);
            $mercurio45->setEstado('P');
            $mercurio45->setFecha($today->format('Y-m-d'));

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, '18001');

            if ($usuario == '') {
                throw new DebugException(
                    'No se puede realizar el registro, no hay usuario disponible para la atención de la solicitud.'.
                        ' Comuniquese con la atencion al cliente',
                    501
                );
            }

            $mercurio45->setUsuario($usuario);
            $mercurio45->setTipo($tipo);
            $mercurio45->setCoddoc($coddoc);
            $mercurio45->setDocumento($documento);

            if (isset($_FILES['archivo_'.$codben]['name']) && $_FILES['archivo_'.$codben]['name'] != '') {
                $extension = explode('.', $_FILES['archivo_'.$codben]['name']);
                $name = $this->tipopc.'_'.$mercurio45->getId().'.'.end($extension);
                $_FILES['archivo_'.$codben]['name'] = $name;

                $uploadFile = new UploadFile;
                $estado = $uploadFile->upload('archivo_'.$codben, 'certificados');

                if ($estado) {
                    $mercurio45->setArchivo($name);
                    $mercurio45->save();

                    $item = Mercurio10::where('tipopc', $this->tipopc)
                        ->where('numero', $mercurio45->getId())
                        ->max('item') + 1;

                    $mercurio10 = new Mercurio10;
                    $mercurio10->setTipopc($this->tipopc);
                    $mercurio10->setNumero($mercurio45->getId());
                    $mercurio10->setItem($item);
                    $mercurio10->setEstado('P');
                    $mercurio10->setNota('Envio a la Caja para verificación');
                    $mercurio10->setFecsis($today->format('Y-m-d'));
                    $mercurio10->save();

                    $message = 'Se adjunto con exito el archivo';
                } else {
                    throw new DebugException('No se cargo: Tamano del archivo muy grande o No es Valido', 501);
                }
            } else {
                throw new DebugException('No se cargo el archivo', 501);
            }

            $response = [
                'success' => true,
                'msj' => $message,
            ];

            $this->db->commit();
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
            $this->db->rollBack();
        }

        return $this->renderObject($response);
    }
}
