<?php

namespace App\Services\Utils;

use Exception;
use Illuminate\Support\Facades\Storage;

class UploadFile
{
    /**
     * Sube un archivo al directorio temporal
     *
     * @param  string  $inputName  Nombre del campo del archivo
     * @param  string  $destination  Directorio destino relativo al disco
     * @param  string|null  $fileName  Nombre de archivo; si es null se genera uno temporal
     * @param  string|null  $disk  Disco de Laravel. Null = comportamiento legado (disco default + prefijo temp/)
     * @return string|false Nombre del archivo subido o false en caso de error
     */
    public static function upload(string $inputName, string $destination = '', ?string $fileName = null, ?string $disk = null)
    {
        if ($disk === 'temp') {
            return self::uploadToStorageTemp($inputName, $destination, $fileName);
        }

        if (! request()->hasFile($inputName)) {
            return false;
        }

        $file = request()->file($inputName);

        if (! $file->isValid()) {
            return false;
        }

        $fileName = $fileName ?: (time().'_'.$file->getClientOriginalName());

        try {
            if ($disk === null) {
                $directory = 'temp/'.trim($destination, '/');
                $stored = $file->storeAs($directory, $fileName);

                return $stored === false ? false : $fileName;
            }

            $stored = $file->storeAs(trim($destination, '/'), $fileName, $disk);

            return $stored === false ? false : $fileName;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Escribe el archivo en storage/temp (no en storage/app).
     */
    private static function uploadToStorageTemp(string $inputName, string $destination, ?string $fileName): string|false
    {
        $relativeDir = trim($destination, '/');
        $absoluteDir = storage_path('temp'.($relativeDir !== '' ? '/'.$relativeDir : ''));

        if (! is_dir($absoluteDir) && ! mkdir($absoluteDir, 0775, true) && ! is_dir($absoluteDir)) {
            return false;
        }

        $file = request()->file($inputName);
        $originalName = $file?->getClientOriginalName() ?: ($_FILES[$inputName]['name'] ?? '');
        $fileName = $fileName ?: (time().'_'.$originalName);
        $absoluteFile = $absoluteDir.DIRECTORY_SEPARATOR.$fileName;

        try {
            if ($file && $file->isValid()) {
                $file->move($absoluteDir, $fileName);
            } elseif (isset($_FILES[$inputName]['tmp_name']) && is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
                if (! move_uploaded_file($_FILES[$inputName]['tmp_name'], $absoluteFile)) {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return is_file($absoluteFile) ? $fileName : false;
    }

    /**
     * Elimina un archivo del storage
     *
     * @param  string  $filePath  Ruta relativa al storage
     */
    public static function delete(string $filePath): bool
    {
        return Storage::delete($filePath);
    }
}
