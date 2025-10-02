<?php

namespace App\Services\PreparaFormularios;

use App\Exceptions\DebugException;

class CifrarDocumento
{

    private $fhash;
    private $algoritmo;
    private $storagePath;

    public function __construct()
    {
        //set default storage path
        $this->storagePath = storage_path('temp/');
    }

    /**
     * Define o algoritmo de criptografia a ser utilizado.
     * @param string $algorithm O algoritmo de criptografia, ex: 'AES-256'
     * @throws Exception Se o algoritmo não for suportado
     */
    public function setAlgoritmo($algorithm)
    {
        $this->algoritmo = $algorithm;
        return $this;
    }

    public function setStoragePath($storagePath)
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    /**
     * cifrar function
     * @changed [2023-12-00]
     * Buscar las claves pública y privada para el firmante.
     * Crear una firma digital utilizando la clave privada y el hash del contenido del archivo PDF.
     * Agregar la firma digital al archivo PDF.
     * Cifrar el archivo PDF utilizando la clave pública del firmante.
     * @author elegroag <elegroag@ibero.edu.co>
     * @return string
     */
    public function cifrar($filename, $strClavePrivada, $claveUsuario)
    {
        if (!$this->algoritmo) $this->algoritmo = OPENSSL_ALGO_SHA256;

        if (strlen($strClavePrivada) < 100) {
            d($strClavePrivada);
            throw new DebugException('La clave privada no es valida.', 501);
        }

        // Contenido del archivo PDF
        $contenidoPDF = file_get_contents($this->storagePath . $filename);
        // Cargar la clave privada
        $keyClavePrivada = openssl_pkey_get_private($strClavePrivada);

        $firmaPrevius = '';

        // adiciona cantidad de firmas en la parte final del PDF
        // Verificar si ya existe una firma en el PDF en sus ultimos 10 caracteres
        $hasFirmaContent = substr($contenidoPDF, -10);
        if (strpos($hasFirmaContent, '#[num:') === false) {
            // Si no existe, inicializar el contador de firmas
            $total_firmas = '001';
        } else {
            // Si existe, extraer el número de firmas y aumentar en 1
            $pos = strpos($hasFirmaContent, '#[num:');
            $xfirmas = substr($hasFirmaContent, $pos + 6, 3);
            $total_firmas = str_pad((int)$xfirmas + 1, 3, '0', STR_PAD_LEFT);
            $hashs = (int)$xfirmas * 256;
            $recorte = $hashs + 10;
            $nofirmado = substr($contenidoPDF, -$recorte);
            $firmaPrevius = substr($nofirmado, 0, -10);
            $contenidoPDF = substr($contenidoPDF, 0, -$recorte);
        }

        // Firmar el hash con la clave privada
        openssl_sign($contenidoPDF, $firmaDigital, $keyClavePrivada, $this->algoritmo);

        // Adjuntar la firma digital al archivo PDF
        $contenidoPDFConFirma = $contenidoPDF . $firmaPrevius .  $firmaDigital . '#[num:' . $total_firmas . ']';

        // Guardar el archivo PDF con la firma digital
        $nombrePdf = strtoupper(uniqid('FSA')) . '.pdf';
        $pathOut = $this->storagePath . $nombrePdf;

        file_put_contents($pathOut, $contenidoPDFConFirma);

        // Liberar la clave privada
        openssl_free_key($keyClavePrivada);

        $this->fhash = bin2hex($firmaDigital);
        return $pathOut;
    }

    /**
     * comprobar function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $pdf
     * @param string $strClavePublica
     * @return int
     */
    public function comprobar($filename, $strClavePublica)
    {
        if (!$this->algoritmo) $this->algoritmo = OPENSSL_ALGO_SHA256;

        $tamanioFirma = 256;
        // Leer el contenido del archivo PDF

        $contenidoPDF = file_get_contents($this->storagePath . $filename);
        // Extraer la firma digital incrustada (últimos bytes del archivo)

        $firmaDigital = substr($contenidoPDF, - ($tamanioFirma), $tamanioFirma);

        // Extraer el contenido del archivo PDF sin la firma
        $contenidoPDFSinFirma = substr($contenidoPDF, 0, - ($tamanioFirma));

        // Cargar la clave pública del firmante
        $keyClavePublica = openssl_pkey_get_public($strClavePublica);

        // Verificar la firma digital
        $resultadoVerificacion = openssl_verify($contenidoPDFSinFirma, $firmaDigital, $keyClavePublica, $this->algoritmo);

        // Liberar la clave pública
        openssl_free_key($keyClavePublica);

        return $resultadoVerificacion;
    }

    public function getFhash()
    {
        return $this->fhash;
    }
}
