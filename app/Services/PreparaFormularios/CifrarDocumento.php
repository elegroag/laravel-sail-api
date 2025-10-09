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
        // set default storage path
        $this->storagePath = storage_path('temp/');
    }

    /**
     * Define o algoritmo de criptografia a ser utilizado.
     *
     * @param  string  $algorithm  O algoritmo de criptografia, ex: 'AES-256'
     *
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
     *
     * @changed [2023-12-00]
     * Buscar las claves pública y privada para el firmante.
     * Crear una firma digital utilizando la clave privada y el hash del contenido del archivo PDF.
     * Agregar la firma digital al archivo PDF.
     * Cifrar el archivo PDF utilizando la clave pública del firmante.
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return string
     */
    public function cifrar($filename, $strClavePrivada, $claveUsuario)
    {
        if (! $this->algoritmo) {
            $this->algoritmo = OPENSSL_ALGO_SHA256;
        }

        if (strlen($strClavePrivada) < 100) {
            d($strClavePrivada);
            throw new DebugException('La clave privada no es valida.', 501);
        }

        $pathOut = null;
        $keyClavePrivada = null;
        try {
            // Contenido del archivo PDF
            $contenidoPDF = file_get_contents($this->storagePath.$filename);
            if ($contenidoPDF === false) {
                throw new DebugException('No se pudo leer el archivo PDF para firmar.', 500);
            }

            // Cargar la clave privada
            $keyClavePrivada = openssl_pkey_get_private($strClavePrivada, $claveUsuario);
            if ($keyClavePrivada === false) {
                throw new DebugException('No se pudo cargar la clave privada.', 500);
            }

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
                $total_firmas = str_pad((int) $xfirmas + 1, 3, '0', STR_PAD_LEFT);
                $hashs = (int) $xfirmas * 256;
                $recorte = $hashs + 10;
                $nofirmado = substr($contenidoPDF, -$recorte);
                $firmaPrevius = substr($nofirmado, 0, -10);
                $contenidoPDF = substr($contenidoPDF, 0, -$recorte);
            }

            // Firmar el hash con la clave privada
            $firmo = openssl_sign($contenidoPDF, $firmaDigital, $keyClavePrivada, $this->algoritmo);
            if ($firmo === false) {
                $err = function_exists('openssl_error_string') ? openssl_error_string() : '';
                throw new DebugException('Fallo al firmar el documento. '.($err ? ('OpenSSL: '.$err) : ''), 500);
            }

            // Adjuntar la firma digital al archivo PDF
            $contenidoPDFConFirma = $contenidoPDF.$firmaPrevius.$firmaDigital.'#[num:'.$total_firmas.']';

            // Guardar el archivo PDF con la firma digital
            $nombrePdf = strtoupper(uniqid('FSA')).'.pdf';
            $pathOut = $this->storagePath.$nombrePdf;

            $ok = file_put_contents($pathOut, $contenidoPDFConFirma);
            if ($ok === false) {
                throw new DebugException('No se pudo escribir el PDF firmado en disco.', 500);
            }

            $this->fhash = bin2hex($firmaDigital);

            return $pathOut;
        } catch (\Throwable $e) {
            throw new DebugException('Error cifrando/firmando el documento: '.$e->getMessage(), 500, $e);
        } finally {
            // Liberar la referencia de la clave privada (openssl_free_key deprecated en PHP 8+)
            $keyClavePrivada = null;
        }
    }

    /**
     * comprobar function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  string  $pdf
     * @param  string  $strClavePublica
     * @return int
     */
    public function comprobar($filename, $strClavePublica)
    {
        if (! $this->algoritmo) {
            $this->algoritmo = OPENSSL_ALGO_SHA256;
        }

        // Cada firma se almacena como 256 bytes, y al final hay un sufijo tipo "#[num:NNN]" (10 chars)
        $tamanioFirma = 256;
        $sufijoLen = 10; // "#[num:NNN]"

        $keyClavePublica = null;
        try {
            // Leer el contenido del archivo PDF
            $contenidoPDF = file_get_contents($this->storagePath.$filename);
            if ($contenidoPDF === false) {
                throw new DebugException('No se pudo leer el archivo PDF para verificar.', 500);
            }

            $len = strlen($contenidoPDF);
            if ($len < $tamanioFirma) {
                // Archivo demasiado pequeño para contener firma(s)
                return 0;
            }

            // Detectar marcador de cantidad de firmas en los últimos 10 caracteres
            $tail10 = substr($contenidoPDF, -$sufijoLen);
            $tieneMarcador = (strpos($tail10, '#[num:') !== false);

            $numFirmas = 1;
            $contenidoBase = $contenidoPDF;
            $bloqueFirmas = '';

            if ($tieneMarcador) {
                // Estructura esperada: [contenidoBase][firmas...][#[num:NNN]]
                // Extraer NNN
                $pos = strpos($tail10, '#[num:');
                $nnn = substr($tail10, $pos + 6, 3);
                if (! ctype_digit($nnn) || (int) $nnn <= 0) {
                    throw new DebugException('Marcador de firmas inválido en el PDF.', 500);
                }
                $numFirmas = (int) $nnn;

                $bytesFirmas = $numFirmas * $tamanioFirma;
                $recorte = $bytesFirmas + $sufijoLen;

                if ($len < $recorte) {
                    throw new DebugException('El tamaño del PDF no coincide con el bloque de firmas indicado.', 500);
                }

                // Extraer bloque de firmas y contenido base
                $bloqueFirmas = substr($contenidoPDF, -$recorte, $bytesFirmas);
                $contenidoBase = substr($contenidoPDF, 0, -$recorte);
            } else {
                // Compatibilidad: documentos antiguos con una firma sin marcador (no esperado por el método cifrar actual)
                $bloqueFirmas = substr($contenidoPDF, -$tamanioFirma, $tamanioFirma);
                $contenidoBase = substr($contenidoPDF, 0, -$tamanioFirma);
                $numFirmas = 1;
            }

            // Cargar la clave pública del firmante
            $keyClavePublica = openssl_pkey_get_public($strClavePublica);
            if ($keyClavePublica === false) {
                throw new DebugException('No se pudo cargar la clave pública.', 500);
            }

            // Verificar cada firma de 256 bytes contra el mismo contenido base
            // Orden en $bloqueFirmas: firma1|firma2|...|firmaN
            $validaAlguna = 0;
            for ($i = 0; $i < $numFirmas; $i++) {
                $offset = $i * $tamanioFirma;
                $firmaDigital = substr($bloqueFirmas, $offset, $tamanioFirma);
                if ($firmaDigital === false || strlen($firmaDigital) !== $tamanioFirma) {
                    continue; // inconsistencia, ignorar este slot
                }

                $resultado = openssl_verify($contenidoBase, $firmaDigital, $keyClavePublica, $this->algoritmo);
                if ($resultado === -1) {
                    $err = function_exists('openssl_error_string') ? openssl_error_string() : '';
                    throw new DebugException('Error de verificación OpenSSL. '.($err ? ('OpenSSL: '.$err) : ''), 500);
                }
                if ($resultado === 1) {
                    $validaAlguna = 1;
                    // No rompemos el bucle para permitir consistencia de verificación completa si se desea
                }
            }

            return $validaAlguna; // 1 si alguna firma coincide, 0 si ninguna
        } catch (\Throwable $e) {
            throw new DebugException('Error comprobando/verificando la firma: '.$e->getMessage(), 500, $e);
        } finally {
            // Liberar la referencia de la clave pública (openssl_free_key deprecated en PHP 8+)
            $keyClavePublica = null;
        }
    }

    public function getFhash()
    {
        return $this->fhash;
    }
}
