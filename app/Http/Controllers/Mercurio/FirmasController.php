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
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * GET /firmas/index
     */
    public function index()
    {
        // Acceso a sesión
        $user = session()->has('user') ? session('user') : [];
        $documento = $user['documento'] ?? null;
        $coddoc = $user['coddoc'] ?? null;

        $mfirma = (new Mercurio16)->findFirst(" documento='{$documento}' AND coddoc='{$coddoc}'");
        $content = $mfirma ? $mfirma->getKeypublic() : null;

        return view('mercurio.firmas.index', [
            'hide_header' => true,
            'title' => 'Firma Dígital',
            'publicKey' => $content,
        ]);
    }

    /**
     * POST /firmas/guardar
     */
    public function guardar(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $user = session()->has('user') ? session('user') : [];
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

            $usuario = (new Mercurio07)->findFirst(" coddoc='{$coddoc}' and documento='{$documento}'");
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
        } catch (\Exception $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * GET /firmas/show
     */
    public function showAction()
    {
        $this->setResponse('ajax');
        try {
            $user = session()->has('user') ? session('user') : [];
            $documento = $user['documento'] ?? null;
            $coddoc = $user['coddoc'] ?? null;

            if (! $documento || ! $coddoc) {
                throw new DebugException('Sesión inválida, no se encontró el usuario.', 401);
            }

            $mfirma = (new Mercurio16)->findFirst(" documento='{$documento}' AND coddoc='{$coddoc}'");
            $firma = ($mfirma && method_exists($mfirma, 'getArray')) ? $mfirma->getArray() : ($mfirma ? [] : false);
            $msj = ($mfirma)
                ? 'OK'
                : 'La firma es requerida para continuar el proceso de solicitud de afiliación, Ahora puedes crear un firma digital por medio de nuestra nueva herramienta.';

            $salida = [
                'success' => true,
                'firma' => $firma,
                'msj' => $msj,
            ];
        } catch (\Exception $err) {
            $salida = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * POST /firmas/valida_firma
     */
    public function validaFirmaAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $user = session()->has('user') ? session('user') : [];
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

            $mfirma = (new Mercurio16)->findFirst("documento='{$documento}' AND coddoc='{$coddoc}'");
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
        } catch (\Exception $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }
}
