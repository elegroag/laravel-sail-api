<?php

namespace App\Services\PreparaFormularios;

use App\Exceptions\DebugException;
use App\Models\Mercurio16;

class GestionFirmas
{

    private $pathOut;
    private $markTime;

    /**
     * lfirma variable
     * @var Mercurio16
     */
    private $lfirma;

    public function __construct($argv)
    {
        $this->pathOut = storage_path('temp/' . $argv['documento'] . 'F' . $argv['coddoc'] . '/');

        if (!is_dir($this->pathOut)) mkdir($this->pathOut, 0776, true);
        if (is_dir($this->pathOut)) chmod($this->pathOut, 0776);
        $this->markTime = strtotime('now');
    }

    /**
     * guardarFirma function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $imagenBase64
     * @param [type] $solicitud
     * @param [type] $representa
     * @return void
     */
    public function guardarFirma($imagenBase64, $usuario)
    {

        $lfirma = (new Mercurio16)->findFirst(" documento='{$usuario->getDocumento()}' AND coddoc='{$usuario->getCoddoc()}'");

        // Decodificar la imagen base64
        $imagen = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenBase64));

        // Generar un nombre único para la imagen
        $nombreImagen = uniqid('FI') . '.png';

        // Ruta donde se guardarán las imágenes
        $rutaImagen = storage_path('temp/' . $nombreImagen);

        // Guardar la imagen en el servidor
        if (file_put_contents($rutaImagen, $imagen)) {
            // La imagen se guardó exitosamente
            $filepath = $this->resizeImagen($rutaImagen);

            $filepath = str_replace(storage_path('temp/'), '', $filepath);

            if ($lfirma) {
                $previus = storage_path('temp/') . $lfirma->getFirma();
                if (file_exists($previus)) unlink($previus);

                $lfirma->setFirma($filepath);
                $lfirma->setFecha(date('Y-m-d'));
            } else {
                $lfirma = new Mercurio16();
                $lfirma->setDocumento($usuario->getDocumento());
                $lfirma->setCoddoc($usuario->getCoddoc());
                $lfirma->setFecha(date('Y-m-d'));
                $lfirma->setFirma($filepath);
                $lfirma->setKeyprivate(null);
                $lfirma->setKeypublic(null);
            }
            $lfirma->save();

            $this->lfirma = (new Mercurio16)->findFirst("
                documento='{$usuario->getDocumento()}' AND
                coddoc='{$usuario->getCoddoc()}'
            ");

            return true;
        } else {
            // Hubo un error al guardar la imagen
            throw new DebugException("Error al guardar la imagen.", 501);
        }
    }

    /**
     * resizeImagen function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $filepath
     * @return string
     */
    public function resizeImagen($filepath)
    {

        if (file_exists($filepath) == false) {
            throw new DebugException("Error la imagen no está disponible para el Resize.", 1);
        }
        // Crear una imagen desde el archivo original
        $imagen = imagecreatefrompng($filepath);

        // Obtener las dimensiones originales de la imagen
        $anchoOriginal = imagesx($imagen);
        $altoOriginal = imagesy($imagen);

        // Calcular el nuevo tamaño con una reducción del 20%
        $nuevoAncho = $anchoOriginal * 0.8; // 80% del ancho original
        $nuevoAlto = $altoOriginal * 0.8; // 80% del alto original

        // Crear una nueva imagen con el nuevo tamaño
        $nuevaImagen = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        imagealphablending($nuevaImagen, false);
        imagesavealpha($nuevaImagen, true);
        $transparente = imagecolorallocatealpha($nuevaImagen, 0, 0, 0, 127);
        imagefill($nuevaImagen, 0, 0, $transparente);

        // Redimensionar la imagen original a la nueva imagen
        imagecopyresampled($nuevaImagen, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

        // Guardar la nueva imagen en un archivo
        $name = basename($filepath);


        $ext = substr(strrchr($name, "."), 1);
        $rename = str_replace($ext, '', $name) . '' . $this->markTime . '.' . $ext;
        $nuevaRuta = $this->pathOut . '' . $rename;
        imagepng($nuevaImagen, $nuevaRuta);

        // Liberar memoria
        imagedestroy($imagen);
        imagedestroy($nuevaImagen);
        return $nuevaRuta;
    }

    /**
     * generarClaves function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return array
     */
    public function generarClaves()
    {
        if ($this->lfirma->getKeyprivate()) {
            if (file_exists(storage_path('temp/' . $this->lfirma->getKeyprivate()))) {
                return array(
                    'private' => $this->lfirma->getKeyprivate(),
                    'public' => $this->lfirma->getKeypublic(),
                );
            }
        }

        // Configuración de la longitud de la clave y el algoritmo
        $longitudClave = 2048; // Puedes ajustar esta longitud según tus necesidades de seguridad
        $algoritmo = OPENSSL_KEYTYPE_RSA;

        // Generar un par de claves pública y privada
        $config = array(
            'private_key_bits' => $longitudClave,
            'private_key_type' => $algoritmo,
        );

        $claves = openssl_pkey_new($config);

        // Extraer la clave privada
        openssl_pkey_export($claves, $clavePrivada);

        // Extraer la clave pública
        $informacionClave = openssl_pkey_get_details($claves);
        $clavePublica = $informacionClave['key'];

        $namePrivada = $this->markTime . '_private.pem';
        $namePublica = $this->markTime . '_public.pem';

        file_put_contents($this->pathOut . $namePrivada,  $clavePrivada);
        file_put_contents($this->pathOut . $namePublica,  $clavePublica);

        $path = str_replace(storage_path('temp/'), '', $this->pathOut . $namePrivada);
        $this->lfirma->setKeyprivate($path);

        $path = str_replace(storage_path('temp/'), '', $this->pathOut . $namePublica);
        $this->lfirma->setKeypublic($path);
        $this->lfirma->save();

        return array(
            'private' => $this->pathOut . $namePrivada,
            'public' => $this->pathOut . $namePublica
        );
    }
}
