<?php

namespace App\Services\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

class UploadFile
{
    /**
     * Sube un archivo al directorio temporal
     *
     * @param string $inputName Nombre del campo del archivo
     * @param string $destination Directorio destino relativo a storage/temp
     * @return string|false Nombre del archivo subido o false en caso de error
     */
    public static function upload(string $inputName, string $destination = '')
    {
        if (!request()->hasFile($inputName)) {
            return false;
        }

        $file = request()->file($inputName);

        // Validar que sea un archivo válido
        if (!$file->isValid()) {
            return false;
        }

        // Generar nombre único para el archivo
        $fileName = time() . '_' . $file->getClientOriginalName();

        try {
            // Guardar en storage/temp/{destination}
            $path = $file->storeAs(
                'temp/' . trim($destination, '/'),
                $fileName
            );

            return $path;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Elimina un archivo del storage
     *
     * @param string $filePath Ruta relativa al storage
     * @return bool
     */
    public static function delete(string $filePath): bool
    {
        return Storage::delete($filePath);
    }
}
