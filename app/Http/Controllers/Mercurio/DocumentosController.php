<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Controller;
use App\Models\Mercurio30;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\FormulariosAdjuntos\EmpresaAdjuntoService;
use App\Services\FormulariosAdjuntos\IndependienteAdjuntoService;
use App\Services\FormulariosAdjuntos\PensionadoAdjuntoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentosController extends Controller
{
    /**
     * POST /mercurio/documentos/ver-pdf
     * Retorna el contenido del archivo como blob para visualización en el frontend
     */
    public function verPdf(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
        ]);

        $filename = basename($request->input('filename'));
        $fichero = storage_path('temp/'.$filename);

        if (! file_exists($fichero)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        return response()->file($fichero, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function downloadDocuments(Request $request)
    {
        $archivo = $request->route('archivo');
        $fichero = public_path('docs/formulario_mercurio/'.$archivo);
        $ext = substr(strrchr($archivo, '.'), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: '.filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit;
        } else {
            redirect('login/index');
            exit();
        }
    }

    /**
     * GET /mercurio/empresa/comprobante/{id}
     */
    public function descargarComprobanteRadicacion(int $id): BinaryFileResponse
    {
        return $this->descargar($id, 'empresa');
    }

    /**
     * GET /mercurio/independiente/comprobante/{id}
     */
    public function descargarComprobanteIndependiente(int $id): BinaryFileResponse
    {
        return $this->descargar($id, 'independiente');
    }

    /**
     * GET /mercurio/pensionado/comprobante/{id}
     */
    public function descargarComprobantePensionado(int $id): BinaryFileResponse
    {
        return $this->descargar($id, 'pensionado');
    }

    private function descargar(int $id, string $tipo): BinaryFileResponse
    {
        $user = session('user');
        if (! $user) {
            abort(401, 'No autorizado');
        }

        [$modelClass, $adjuntoService] = match ($tipo) {
            'empresa' => [Mercurio30::class, EmpresaAdjuntoService::class],
            'independiente' => [Mercurio41::class, IndependienteAdjuntoService::class],
            'pensionado' => [Mercurio38::class, PensionadoAdjuntoService::class],
            default => abort(404, 'Tipo de comprobante no soportado'),
        };

        $solicitud = $modelClass::where('id', $id)
            ->where('documento', $user['documento'])
            ->where('coddoc', $user['coddoc'])
            ->first();

        if (! $solicitud) {
            abort(404, 'Solicitud no encontrada');
        }

        $filename = $adjuntoService::generarComprobanteRadicacion($solicitud);
        $fichero = storage_path('temp/'.$filename);

        if (! file_exists($fichero)) {
            abort(404, 'Comprobante no encontrado');
        }

        return response()->file($fichero, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
