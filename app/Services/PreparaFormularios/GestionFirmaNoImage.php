<?php

namespace App\Services\PreparaFormularios;

class GestionFirmaNoImage
{

    private $pathOut;
    private $documento;
    private $coddoc;

    /**
     * lfirma variable
     * @var Mercurio16
     */
    private $lfirma;

    public function __construct($param)
    {
        $this->documento = $param['documento'];
        $this->coddoc = $param['coddoc'];

        $this->pathOut = Core::getInitialPath() . 'storage/' . $this->documento . 'F' . $this->coddoc . '/';
        if (!is_dir($this->pathOut)) mkdir($this->pathOut, 0775, true);
    }

    public function hasFirma()
    {
        $has = ((new Mercurio16)->count(
            "*",
            "conditions: documento='{$this->documento}' AND coddoc='{$this->coddoc}'"
        ) > 0) ? true : false;
        if ($has) {
            $this->lfirma = (new Mercurio16)->findFirst("documento='{$this->documento}' AND coddoc='{$this->coddoc}'");
        }
        return $has;
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
    public function guardarFirma()
    {
        $this->lfirma = (new Mercurio16)->findFirst("documento='{$this->documento}' AND coddoc='{$this->coddoc}'");
        if (!$this->lfirma) {
            $this->lfirma = new Mercurio16();
            $this->lfirma->setDocumento($this->documento);
            $this->lfirma->setCoddoc($this->coddoc);
            $this->lfirma->setFecha(date('Y-m-d'));
            $this->lfirma->setFirma('N/A');
            $this->lfirma->setKeyprivate(null);
            $this->lfirma->setKeypublic(null);
            $this->lfirma->save();
        }
        return true;
    }


    /**
     * generarClaves function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return array
     */
    public function generarClaves()
    {
        if ($this->lfirma->getKeyprivate() && $this->lfirma->getKeypublic()) {
            return array(
                'private' => $this->lfirma->getKeyprivate(),
                'public' => $this->lfirma->getKeypublic(),
            );
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

        $this->lfirma->setKeyprivate($clavePrivada);
        $this->lfirma->setKeypublic($clavePublica);
        $this->lfirma->save();

        return array(
            'private' => $this->clavePrivada,
            'public' => $this->clavePublica
        );
    }

    /**
     * getFirma function
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @return Mercurio16
     */
    public function getFirma()
    {
        return $this->lfirma;
    }
}
