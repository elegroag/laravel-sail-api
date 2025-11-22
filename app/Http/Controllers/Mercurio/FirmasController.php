<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Services\PreparaFormularios\CifrarDocumento;
use App\Services\PreparaFormularios\GestionFirmas;
use Illuminate\Http\Request;

class FirmasController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    /**
     * GET /firmas/index
     */
    public function index()
    {
        try {
            $user = $this->user ?? [];
            $documento = $user['documento'] ?? null;
            $coddoc = $user['coddoc'] ?? null;

            $mfirma = Mercurio16::where('documento', $documento)->where('coddoc', $coddoc)->first();
            $content = $mfirma ? $mfirma->getKeypublic() : null;

            return view('mercurio.firmas.index', [
                'hide_header' => true,
                'title' => 'Firma Dígital',
                'publicKey' => $content,
            ]);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $salida['code'],
            ]);
            return redirect()->route('principal/index');
        }
    }

    /**
     * POST /firmas/guardar
     */
    public function guardar(Request $request)
    {
        $this->db->begin();
        try {
            $user = $this->user ?? [];
            $documento = $user['documento'] ?? null;
            $coddoc = $user['coddoc'] ?? null;
            $clave = $user['clave'] ?? null;

            if (! $documento || ! $coddoc) {
                throw new DebugException('Sesión inválida, no se encontró el usuario.', 401);
            }

            $gestionFirmas = new GestionFirmas([
                'documento' => $documento,
                'coddoc' => $coddoc,
            ]);

            $usuario = Mercurio07::where('coddoc', $coddoc)->where('documento', $documento)->first();
            if (! $usuario) {
                throw new DebugException('No fue posible identificar el usuario.', 404);
            }

            $imagen = $request->input('imagen');
            if (! $imagen) {
                throw new DebugException('Error la imagen no está disponible', 422);
            }

            $gestionFirmas->guardarFirma($imagen, $usuario);
            $gestionFirmas->generarClaves($clave);

            $salida = ['success' => true, 'msj' => 'Imagen guardada correctamente.'];
            $this->db->commit();
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $this->db->rollBack();
        }

        return response()->json($salida);
    }

    /**
     * GET /firmas/show
     */
    public function show()
    {
        try {
            $user = $this->user ?? [];
            $documento = $user['documento'] ?? null;
            $coddoc = $user['coddoc'] ?? null;

            if (! $documento || ! $coddoc) {
                throw new DebugException('Sesión inválida, no se encontró el usuario.', 401);
            }

            $mfirma = Mercurio16::where('documento', $documento)->where('coddoc', $coddoc)->first();
            $firma = ($mfirma && method_exists($mfirma, 'getArray')) ? $mfirma->getArray() : ($mfirma ? [] : false);
            $msj = ($mfirma)
                ? 'OK'
                : 'La firma es requerida para continuar el proceso de solicitud de afiliación, Ahora puedes crear un firma digital por medio de nuestra nueva herramienta.';

            $salida = [
                'success' => true,
                'firma' => $firma,
                'msj' => $msj,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    /**
     * POST /firmas/valida_firma
     */
    public function validaFirma(Request $request)
    {
        $this->db->begin();
        try {
            $user = $this->user ?? [];
            $coddoc = $user['coddoc'] ?? null;
            $documento = $user['documento'] ?? null;

            if (! $documento || ! $coddoc) {
                throw new DebugException('Sesión inválida, no se encontró el usuario.', 401);
            }

            if (! $request->hasFile('file') || ! $request->file('file')->isValid()) {
                throw new DebugException('Error el documento no es válido', 422);
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $name = strtoupper(uniqid('TMP_')) . '_' . time() . '.' . $extension;

            $dir = public_path('temp');
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $file->move($dir, $name);

            $mfirma = Mercurio16::where('documento', $documento)->where('coddoc', $coddoc)->first();
            if (! $mfirma) {
                throw new DebugException('No existe firma registrada para el usuario.', 404);
            }

            $pdf = $dir . DIRECTORY_SEPARATOR . $name;
            $cifrarDocumento = new CifrarDocumento;
            $out = $cifrarDocumento->comprobar($pdf, $mfirma->getKeypublic());

            $response = [
                'success' => true,
                'isValid' => ($out) ? true : false,
                'msj' => ($out)
                    ? 'El documento es válido, se ha comprobado la autenticidad del contenido del documento.'
                    : 'El documento no es válido, el documento se ha modificado y no es auténtico.',
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
            $this->db->rollBack();
        }

        return response()->json($response);
    }
}
