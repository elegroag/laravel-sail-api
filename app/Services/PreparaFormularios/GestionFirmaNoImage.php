<?php

namespace App\Services\PreparaFormularios;

use App\Models\Mercurio16;

class GestionFirmaNoImage
{
    private $pathOut;

    private $documento;

    private $coddoc;

    private $password;

    /**
     * lfirma variable
     *
     * @var Mercurio16
     */
    private $lfirma;

    public function __construct($param)
    {
        $this->documento = $param['documento'];
        $this->coddoc = $param['coddoc'];
        $this->password = $param['password'];

        $this->pathOut = storage_path('temp/' . $this->documento . 'F' . $this->coddoc . '/');
        if (! is_dir($this->pathOut)) {
            mkdir($this->pathOut, 0775, true);
        }
    }

    public function hasFirma()
    {
        $has = (Mercurio16::where('documento', $this->documento)
            ->where('coddoc', $this->coddoc)
            ->count() > 0) ? true : false;

        if ($has) {
            $this->lfirma = Mercurio16::where('documento', $this->documento)
                ->where('coddoc', $this->coddoc)
                ->first();
        }

        return $has;
    }

    /**
     * guardarFirma function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $imagenBase64
     * @param [type] $solicitud
     * @param [type] $representa
     * @return void
     */
    public function guardarFirma()
    {
        $this->lfirma = Mercurio16::where('documento', $this->documento)
            ->where('coddoc', $this->coddoc)
            ->first();

        if (! $this->lfirma) {
            $this->lfirma = new Mercurio16(
                [
                    'documento' => $this->documento,
                    'coddoc' => $this->coddoc,
                    'password' => $this->password,
                    'fecha' => date('Y-m-d'),
                    'firma' => 'N/A',
                    'keyprivate' => null,
                    'keypublic' => null
                ]
            );
            $this->lfirma->save();
        }

        return true;
    }

    /**
     * generarClaves function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return array
     */
    public function generarClaves()
    {
        if ($this->lfirma) {
            if ($this->lfirma->getKeyprivate() && $this->lfirma->getKeypublic()) {
                return [
                    'private' => $this->lfirma->getKeyprivate(),
                    'public' => $this->lfirma->getKeypublic(),
                ];
            }
        } else {
            $this->lfirma = new Mercurio16(
                [
                    'documento' => $this->documento,
                    'coddoc' => $this->coddoc,
                    'password' => $this->password,
                    'fecha' => date('Y-m-d'),
                    'firma' => 'N/A',
                    'keyprivate' => null,
                    'keypublic' => null,
                ]
            );
        }

        // Configuración de la longitud de la clave y el algoritmo
        $longitudClave = 2048; // Puedes ajustar esta longitud según tus necesidades de seguridad
        $algoritmo = OPENSSL_KEYTYPE_RSA;

        // Generar un par de claves pública y privada
        $config = [
            'private_key_bits' => $longitudClave,
            'private_key_type' => $algoritmo,
        ];

        $claves = openssl_pkey_new($config);

        // Extraer la clave privada
        openssl_pkey_export($claves, $clavePrivada, $this->password);

        // Extraer la clave pública
        $informacionClave = openssl_pkey_get_details($claves);
        $clavePublica = $informacionClave['key'];

        $this->lfirma->setKeyprivate($clavePrivada);
        $this->lfirma->setKeypublic($clavePublica);
        $this->lfirma->save();

        return [
            'private' => $clavePrivada,
            'public' => $clavePublica,
        ];
    }

    /**
     * getFirma function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return Mercurio16
     */
    public function getFirma()
    {
        return $this->lfirma;
    }
}
