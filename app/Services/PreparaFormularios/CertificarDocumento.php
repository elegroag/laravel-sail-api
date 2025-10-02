<?php

namespace App\Services\PreparaFormularios;

use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;

class CertificarDocumento
{
    private $pathOut;
    private $documento;
    private $coddoc;

    public function __construct()
    {
        $user = session('user');
        $this->documento = $user['documento'];
        $this->coddoc = $user['coddoc'];

        $this->pathOut = storage_path('temp/' . $this->documento . 'F' . $this->coddoc . '/');
        if (!is_dir($this->pathOut)) mkdir($this->pathOut, 0775, true);
    }

    /**
     * Firma digitalmente un PDF existente usando TCPDF + FPDI.
     *
     * @param string $filename          Ruta absoluta del PDF existente a firmar
     * @param string $strClavePrivada   Contenido del certificado/clave (PEM) o del contenedor P12/PFX (binario/base64)
     * @param string $claveUsuario      Contraseña del P12/PFX o de la clave privada PEM (si aplica)
     * @return string                   Ruta del PDF firmado
     * @throws Exception
     */
    public function certificar($filename, $strClavePrivada, $claveUsuario)
    {
        // Validaciones básicas
        if (!file_exists($filename)) {
            throw new Exception("El archivo PDF a firmar no existe: {$filename}");
        }

        // Validar dependencia FPDI-TCPDF
        if (!class_exists(Fpdi::class)) {
            throw new Exception('No se encontró FPDI-TCPDF. Instale con: composer require setasign/fpdi-tcpdf:^2.4');
        }

        // Preparar archivos temporales para cert y key en formato PEM
        $certPemPath = $this->pathOut . 'cert_tmp.pem';
        $keyPemPath  = $this->pathOut . 'key_tmp.pem';
        $createdCert = false;
        $createdKey  = false;

        try {
            // Detectar si $strClavePrivada es PEM directo o P12/PFX (binario/base64)
            $isPem = str_contains($strClavePrivada, '-----BEGIN');

            if ($isPem) {
                // Si ya es PEM, asumimos que incluye CERT y KEY o al menos KEY + CERT por separado
                // Guardamos tal cual y, si están combinados, TCPDF puede leer cert/key por separado.
                // Intentamos separar si vienen juntos.
                $certContent = '';
                $keyContent  = '';

                // Extraer bloques PEM
                if (preg_match('/(-----BEGIN CERTIFICATE-----[\s\S]+?-----END CERTIFICATE-----)/', $strClavePrivada, $m)) {
                    $certContent = $m[1];
                }
                if (preg_match('/(-----BEGIN (ENCRYPTED )?PRIVATE KEY-----[\s\S]+?-----END (ENCRYPTED )?PRIVATE KEY-----)/', $strClavePrivada, $m)) {
                    $keyContent = $m[1];
                }

                // Si no se pudo separar, guardamos todo como cert y como key (casos PEM combinados no estándar)
                if ($certContent === '' && $keyContent === '') {
                    $certContent = $strClavePrivada;
                    $keyContent  = $strClavePrivada;
                }

                file_put_contents($certPemPath, $certContent);
                file_put_contents($keyPemPath, $keyContent);
                $createdCert = $createdKey = true;
            } else {
                // Asumimos P12/PFX en binario o base64
                $p12Data = $strClavePrivada;
                // Intentar decodificar base64 si luce como base64
                if (preg_match('/^[A-Za-z0-9+\/\r\n]+=*$/', trim($p12Data)) && !str_contains($p12Data, ' ')) {
                    $decoded = base64_decode($p12Data, true);
                    if ($decoded !== false) {
                        $p12Data = $decoded;
                    }
                }

                $certs = [];
                if (!openssl_pkcs12_read($p12Data, $certs, $claveUsuario)) {
                    throw new Exception('No fue posible leer el contenedor PKCS#12 (P12/PFX). Verifique la contraseña.');
                }

                if (empty($certs['cert']) || empty($certs['pkey'])) {
                    throw new Exception('El P12/PFX no contiene certificado o clave privada válidos.');
                }

                // Guardar a PEM
                file_put_contents($certPemPath, $certs['cert']);
                file_put_contents($keyPemPath, $certs['pkey']);
                $createdCert = $createdKey = true;
            }

            // Crear instancia FPDI (extiende TCPDF)
            $pdf = new Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Configurar la firma digital en TCPDF
            // $claveUsuario aplica para claves privadas protegidas; si la key PEM no tiene pass, puede ir ''
            $info = [
                'Name' => 'Firma Digital',
                'Location' => 'Sistema',
                'Reason' => 'Aprobación de documento',
                'ContactInfo' => 'noreply@example.com',
            ];
            // Importante: usar esquema file:// para que OpenSSL interprete rutas de archivo
            $pdf->setSignature('file://' . $certPemPath, 'file://' . $keyPemPath, $claveUsuario ?? '', '', 2, $info);

            // Importar PDF existente con FPDI y replicar páginas
            $pageCount = $pdf->setSourceFile($filename);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size  = $pdf->getTemplateSize($tplId);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);

                // En la última página definimos la apariencia visible de la firma (esquina inferior derecha)
                if ($pageNo === $pageCount) {
                    $margin = 10; // mm
                    $sigW = 50;   // ancho de la caja de firma
                    $sigH = 20;   // alto de la caja de firma
                    $x = $size['width'] - $sigW - $margin;
                    $y = $size['height'] - $sigH - $margin;
                    $pdf->setSignatureAppearance($x, $y, $sigW, $sigH);

                    // Opcional: dibujar un borde/etiqueta para visualizar la firma
                    $pdf->SetDrawColor(50, 50, 50);
                    $pdf->Rect($x, $y, $sigW, $sigH);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->SetXY($x + 2, $y + 2);
                    $pdf->MultiCell($sigW - 4, 5, "Firma Digital\nDocumento firmado electrónicamente", 0, 'L');
                }
            }

            // Generar salida
            $outPath = $this->pathOut . pathinfo($filename, PATHINFO_FILENAME) . '_firmado.pdf';
            $pdf->Output($outPath, 'F');

            return $outPath;
        } catch (Exception $e) {
            throw $e;
        } finally {
            // Limpieza de archivos temporales
            if ($createdCert && file_exists($certPemPath)) @unlink($certPemPath);
            if ($createdKey && file_exists($keyPemPath)) @unlink($keyPemPath);
        }
    }
}
