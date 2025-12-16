<?php

namespace App\Services\Certificados;

use App\Models\Gener09;
use App\Services\LegacyDatabaseService;
use Carbon\Carbon;

/**
 * Clase para obtener datos de certificados de trabajador
 * Implementa la interfaz necesaria para ser usada por Certificado
 */
class CertiEmpleador
{
    public $nit;
    public $tipo;
    public $data = [];
    public $template = '';

    /**
     * Constructor que inicializa los datos según el tipo de certificado
     * @param string $cedtra Cédula del trabajador
     * @param string $tipo Tipo de certificado (A=principal, I=nucleo, T=multi, P=planillas)
     */
    public function __construct($nit, $tipo)
    {
        $this->nit = $nit;
        $this->tipo = $tipo;

        switch ($tipo) {
            case 'A': // Certificado Afiliación Principal
                $this->data = $this->empresaPrincipal();
                $this->template = 'oficios.certificados.tmp_empresa_principal';
                break;
            case 'I': // Certificación con Núcleo
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
        return "certificado_{$this->nit}_" . date('YmdHis') . ".pdf";
    }

    /**
     * Obtiene datos del trabajador principal y su trayectoria laboral
     */
    public function empresaPrincipal(): array
    {
        $legacy = new LegacyDatabaseService('comfaca');
        $empleador = $legacy->select("SELECT 
            subsi02.*, 
            subsi48.codsuc, 
            subsi48.calsuc,
            subsi48.codciu,
            CASE 
                WHEN subsi02.estado = 'A' || subsi02.estado = 'D'  THEN 'ACTIVO'
                WHEN subsi02.estado = 'I' THEN 'INACTIVO'
                WHEN subsi02.estado = 'S' THEN 'SUSPENDIDA'
                ELSE 'OTRO' 
            END as estado_detalle
            FROM subsi02 
            INNER JOIN subsi48 ON subsi02.nit = subsi48.nit 
            WHERE subsi02.nit = ?", [$this->nit])->first();

        $q1 = $legacy->select("SELECT COUNT(*) as numtra FROM subsi15 WHERE subsi15.estado=? and nit=? AND codsuc=? ", ['A', $this->nit, $empleador['codsuc']])->first();
        $q2 = $legacy->select("SELECT COUNT(*) as numtra FROM subsi168 WHERE estado=? and nit=? AND codsuc=? ", ['A', $this->nit, $empleador['codsuc']])->first();
        $q3 = $legacy->select("SELECT MAX(perapo) as ultper FROM subsi64 WHERE nit=? AND codsuc=?", [$this->nit, $empleador['codsuc']])->first();

        $codciu = Gener09::where('codzon', $empleador['codciu'])->first();
        $empleador['numtrab'] = $q1['numtra'] + $q2['numtra'];
        $empleador['ultper'] = $q3['ultper'];
        $empleador['ciudad'] = $codciu['detzon'];
        $fecha_afi = Carbon::parse($empleador['fecafi'])->locale('es');
        $empleador['fecha_afiliacion'] = 'día ' . $fecha_afi->translatedFormat('d') . ' del mes ' . $fecha_afi->translatedFormat('F') . ' y año ' . $fecha_afi->translatedFormat('Y');

        $legacy->disconnect();
        return [
            'empleador' => (object) $empleador,
            'fecha' => date('Y-m-d'),
        ];
    }
}
