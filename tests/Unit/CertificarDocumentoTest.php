<?php

namespace Tests\Unit;

use App\Services\PreparaFormularios\CertificarDocumento;
use Tests\TestCase;

class CertificarDocumentoTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function firma_un_pdf_usando_pem_generado_en_tiempo_de_test()
    {
        // 1) Preparar sesión requerida por CertificarDocumento
        session([
            'user' => [
                'documento' => '12345678',
                'coddoc' => 'ABC',
            ],
        ]);

        // 2) Crear un PDF de prueba en storage
        $basePath = storage_path('app/testing/certificar');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }
        $inputPdf = $basePath . '/input.pdf';

        // Generar PDF simple con TCPDF para asegurar compatibilidad
        $tcpdf = new \TCPDF();
        $tcpdf->setPrintHeader(false);
        $tcpdf->setPrintFooter(false);
        $tcpdf->AddPage();
        $tcpdf->SetFont('helvetica', '', 12);
        $tcpdf->Cell(0, 10, 'PDF de prueba para firma', 0, 1, 'L');
        $tcpdf->Output($inputPdf, 'F');
        $this->assertFileExists($inputPdf, 'No se pudo crear el PDF de entrada');

        // 3) Generar clave privada y certificado autofirmado (PEM) protegidos con password
        $passphrase = 'pass123';
        $privkey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $this->assertNotFalse($privkey, 'No se pudo generar la clave privada');

        $csr = openssl_csr_new([
            'commonName' => 'Testing Cert',
            'organizationName' => 'Acme Inc',
            'countryName' => 'MX',
        ], $privkey, ['digest_alg' => 'sha256']);
        $this->assertNotFalse($csr, 'No se pudo generar el CSR');

        $sscert = openssl_csr_sign($csr, null, $privkey, 365, ['digest_alg' => 'sha256']);
        $this->assertNotFalse($sscert, 'No se pudo firmar el certificado');

        $certPem = '';
        $keyPem = '';
        $this->assertTrue(openssl_x509_export($sscert, $certPem), 'No se pudo exportar el certificado a PEM');
        $this->assertTrue(openssl_pkey_export($privkey, $keyPem, $passphrase), 'No se pudo exportar la clave privada en PEM');

        // Combinar en un único string PEM (la clase sabe separar)
        $pemCombined = $certPem . "\n" . $keyPem;

        // 4) Ejecutar la firma
        $servicio = new CertificarDocumento();
        $signedPath = $servicio->certificar($inputPdf, $pemCombined, $passphrase);

        // 5) Validaciones del archivo de salida
        $this->assertFileExists($signedPath, 'No se generó el PDF firmado');
        $this->assertStringStartsWith('%PDF', file_get_contents($signedPath), 'El archivo resultante no parece un PDF');
        $this->assertGreaterThan(filesize($inputPdf), filesize($signedPath), 'El PDF firmado debería ser mayor al original');

        // Limpieza de artefactos del test
        //@unlink($inputPdf);
        //@unlink($signedPath);
    }
}
