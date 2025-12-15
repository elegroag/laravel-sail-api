<?php

namespace App\Services\Certificados;

use App\Services\LegacyDatabaseService;

/**
 * Clase para obtener datos de certificados de trabajador
 * Implementa la interfaz necesaria para ser usada por Certificado
 */
class CertiTrabajador
{
    public $cedtra;
    public $tipo;
    public $data = [];
    public $template = '';

    /**
     * Constructor que inicializa los datos según el tipo de certificado
     * @param string $cedtra Cédula del trabajador
     * @param string $tipo Tipo de certificado (A=principal, I=nucleo, T=multi, P=planillas)
     */
    public function __construct($cedtra, $tipo)
    {
        $this->cedtra = $cedtra;
        $this->tipo = $tipo;

        switch ($tipo) {
            case 'A': // Certificado Afiliación Principal
                $this->data = $this->trabajadorPrincipal();
                $this->template = 'oficios.certificados.tmp_trabajador_principal';
                break;
            case 'I': // Certificación con Núcleo
                $this->data = $this->nucleoFamiliar();
                $this->template = 'oficios.certificados.tmp_trabajador_nucleo';
                break;
            case 'T': // Certificación de Multiafiliación
                $this->data = $this->multiAfiliacion();
                $this->template = 'oficios.certificados.tmp_trabajador_multiafiliacion';
                break;
            case 'P': // Reporte trabajador en planillas
                $this->data = $this->trabajadorPlanillas();
                $this->template = 'oficios.certificados.tmp_trabajador_planillas';
                break;
            default:
                throw new \InvalidArgumentException("Tipo de certificado no válido: {$tipo}");
        }
    }

    /**
     * Retorna el nombre de la plantilla Blade a usar
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Retorna los datos para renderizar la plantilla
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Retorna el nombre del archivo PDF a generar
     */
    public function getFileName(): string
    {
        return "certificado_trabajador_{$this->cedtra}_" . date('YmdHis') . ".pdf";
    }

    /**
     * Obtiene datos del trabajador principal y su trayectoria laboral
     */
    public function trabajadorPrincipal(): array
    {
        $legacy = new LegacyDatabaseService('comfaca');
        $trabajador = $legacy->select('SELECT subsi15.*,
        CONCAT_WS(" ", subsi15.prinom, subsi15.segnom, subsi15.priape, subsi15.segape) as nomtra, 
        subsi02.razsoc 
        FROM subsi15 
        LEFT JOIN subsi02 ON subsi02.nit = subsi15.nit 
        WHERE subsi15.cedtra = ?', [$this->cedtra]);


        $trayectorias = $legacy->select('SELECT subsi16.*, 
        subsi02.razsoc 
        FROM subsi16 
        INNER JOIN subsi02 ON subsi02.nit = subsi16.nit 
        WHERE subsi16.cedtra = ?', [$this->cedtra]);
        $legacy->disconnect();

        // Convertir Collection a objeto para usar en la plantilla Blade
        $trabajadorObj = $trabajador->isNotEmpty() ? (object) $trabajador->first() : null;

        // Convertir Collection de trayectorias a array de objetos
        $trayectoriasObj = $trayectorias->map(fn($t) => (object) $t)->toArray();

        return [
            'trabajador' => $trabajadorObj,
            'trayectorias' => $trayectoriasObj,
            'fecha' => date('Y-m-d'),
        ];
    }

    public function nucleoFamiliar()
    {
        $legacy = new LegacyDatabaseService('comfaca');
        $relacion_tc = $legacy->select('SELECT * FROM subsi21 WHERE cedtra = ' . $this->cedtra);
        $conyuge = $legacy->select('SELECT * FROM subsi20 WHERE cedcon = ' . $relacion_tc[0]['cedcon']);
        $beneficiario_tb = $legacy->select('SELECT * FROM subsi23 WHERE cedtra = ' . $this->cedtra);
        $beneficiario = $legacy->select('SELECT * FROM subsi22 WHERE codben = ' . $beneficiario_tb[0]['codben']);

        $legacy->disconnect();
        return [
            'relacion_tc' => $relacion_tc,
            'conyuge' => $conyuge,
            'beneficiario_tb' => $beneficiario_tb,
            'beneficiario' => $beneficiario
        ];
    }

    public function multiAfiliacion()
    {
        $legacy = new LegacyDatabaseService('comfaca');
        $row = $legacy->select('SELECT * FROM subsi136 WHERE cedtra = ' . $this->cedtra);
        $legacy->disconnect();
        return $row;
    }

    public function trabajadorPlanillas()
    {
        $legacy = new LegacyDatabaseService('comfaca');
        $row = $legacy->select('SELECT * FROM subsi64 WHERE cedtra = ' . $this->cedtra);
        $legacy->disconnect();
        return $row;
    }
}
