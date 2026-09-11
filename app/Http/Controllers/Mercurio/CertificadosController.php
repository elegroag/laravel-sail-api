<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Mercurio10;
use App\Models\Mercurio45;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Logger;
use App\Services\Utils\UploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CertificadosController extends ApplicationController
{
    protected $tipopc = '8';


    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        try {
            $ps = new ApiSubsidio;
            $ps->send(
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
                $certificadosPresentados = Mercurio45::query()
                    ->where('documento', $this->user['documento'])
                    ->where('estado', 'P')
                    ->orderByDesc('fecha')
                    ->orderByDesc('id')
                    ->get();

                foreach ($beneficiarios as $ai => $beneficiario) {
                    $pendientes = Mercurio45::query()
                        ->where('codben', $beneficiario['codben'])
                        ->where('documento', $this->user['documento'])
                        ->where('estado', 'P')
                        ->get();

                    if ($pendientes->isNotEmpty()) {
                        $beneficiarios[$ai]['certificadoPendiente'] = true;
                        $beneficiarios[$ai]['certificados'] = $pendientes;
                    } else {
                        $beneficiarios[$ai]['certificadoPendiente'] = false;
                        $beneficiarios[$ai]['certificados'] = collect();
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
        } catch (\Throwable $e) {
            $salida = $this->captureException($e);
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->route('principal/index');
        }
    }

    public function guardar(Request $request)
    {
        $message = '';
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $codben = $request->input('codben');
            $nombre = $request->input('nombre');
            $codcer = $request->input('codcer');
            $nomcer = $request->input('nomcer');

            if (
                Mercurio45::where('codben', $codben)
                ->where('codcer', $codcer)
                ->where('estado', '!=', 'X')
                ->count() > 0
            ) {
                $response = [
                    'success' => false,
                    'msj' => 'Ya tiene un certificado presentando, por favor espere a su aprobacion',
                ];

                DB::rollBack();

                return response()->json($response);
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
                    'No se puede realizar el registro, no hay usuario disponible para la atención de la solicitud.' .
                        ' Comuniquese con la atencion al cliente',
                    501
                );
            }

            $mercurio45->setUsuario($usuario);
            $mercurio45->setTipo($tipo);
            $mercurio45->setCoddoc($coddoc);
            $mercurio45->setDocumento($documento);
            $mercurio45->save();
            $mercurio45->assignRuuidIfMissing();
            $mercurio45->setFecsol($today->format('Y-m-d'));

            $inputName = 'archivo_' . $codben;
            if (isset($_FILES[$inputName]['name']) && $_FILES[$inputName]['name'] != '') {
                $extension = strtolower((string) pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
                if ($extension !== 'pdf') {
                    throw new DebugException('Solo se admiten archivos PDF', 501);
                }

                $ruuid = (string) $mercurio45->getRuuid();
                if ($ruuid === '') {
                    throw new DebugException('No se pudo generar el radicado del certificado.', 501);
                }

                $name = $ruuid . '.' . $extension;
                $estado = UploadFile::upload($inputName, '', $name, 'temp');

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
                    if ($mercurio45->ruuid) {
                        $mercurio10->setRuuid($mercurio45->ruuid . '-' . str_pad((string) $item, 2, '0', STR_PAD_LEFT));
                    }
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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function borrar(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = (int) $request->input('id');
            $documento = $this->user['documento'] ?? null;

            if (! $id || ! $documento) {
                throw new DebugException('Solicitud no válida', 400);
            }

            $mercurio45 = Mercurio45::query()
                ->where('id', $id)
                ->where('documento', $documento)
                ->first();

            if (! $mercurio45) {
                throw new DebugException('No se encontró la solicitud de certificado', 404);
            }

            if (! in_array($mercurio45->getEstado(), ['P', 'D', 'T'], true)) {
                throw new DebugException('Solo se pueden eliminar solicitudes pendientes, temporales o devueltas', 403);
            }

            $archivo = $mercurio45->getArchivo();

            // El trigger de mercurio45 se encarga del archivado al eliminar
            Mercurio45::where('id', $id)
                ->where('documento', $documento)
                ->delete();

            $this->eliminarArchivoCertificado($archivo);

            DB::commit();

            return response()->json([
                'success' => true,
                'msj' => 'La solicitud de certificado fue eliminada correctamente',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->handleException($e, $request);
        }
    }

    private function eliminarArchivoCertificado(?string $archivo): void
    {
        if (! $archivo) {
            return;
        }

        $candidatos = [
            storage_path('temp/' . $archivo),
            storage_path('app/temp/certificados/' . $archivo),
            storage_path('temp/certificados/' . $archivo),
            public_path('temp/' . $archivo),
            public_path('temp/certificados/' . $archivo),
        ];

        foreach ($candidatos as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        UploadFile::delete($archivo);
        Storage::disk('temp')->delete($archivo);
    }
}
