<?php

namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $fichero = storage_path('temp/' . $filename);

        if (! file_exists($fichero)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
        ];
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        return response()->file($fichero, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadDocuments(Request $request)
    {
        $archivo = $request->route('archivo');
        $fichero = public_path('docs/formulario_mercurio/' . $archivo);
        $ext = substr(strrchr($archivo, '.'), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit;
        } else {
            redirect('login/index');
            exit();
        }
    }
}
