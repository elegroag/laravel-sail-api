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
                $this->template = 'oficios.certificados.tmp_trabajador_aportes';
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
        CASE
            WHEN subsi15.estado = "A" THEN "ACTIVO"
            WHEN subsi15.estado = "B" THEN "INACTIVO"
            WHEN subsi15.estado = "M" THEN "MUERTO"
            ELSE "OTRO"
        END as estado_detalle,
        CONCAT_WS(" ", subsi15.prinom, subsi15.segnom, subsi15.priape, subsi15.segape) as nomtra, 
        subsi02.razsoc,
        "" as ultper ,
        "" as estapo,
        "NO" as calemp
        FROM subsi15 
        LEFT JOIN subsi02 ON subsi02.nit = subsi15.nit 
        WHERE subsi15.cedtra = ?', [$this->cedtra]);


        $trayectorias = $legacy->select('SELECT subsi16.*, 
        subsi02.razsoc 
        FROM subsi16 
        INNER JOIN subsi02 ON subsi02.nit = subsi16.nit 
        WHERE subsi16.cedtra = ?', [$this->cedtra]);

        $ultper = $legacy->select('SELECT MAX(subsi64.perapo) as ultper 
        FROM subsi65 
        INNER JOIN subsi64 ON subsi64.numero = subsi65.numero 
        WHERE subsi65.cedtra = ?', [$this->cedtra]);

        $legacy->disconnect();

        // Convertir Collection a objeto para usar en la plantilla Blade
        $trabajadorObj = $trabajador->isNotEmpty() ? (object) $trabajador->first() : null;

        // Convertir Collection de trayectorias a array de objetos
        $trayectoriasObj = $trayectorias->map(fn($t) => (object) $t)->toArray();

        $trabajadorObj->ultper = $ultper->isNotEmpty() ? $ultper->first()['ultper'] : "";
        $trabajadorObj->estapo = "Al día";
        $trabajadorObj->calemp = "NO";

        return [
            'trabajador' => $trabajadorObj,
            'trayectorias' => $trayectoriasObj,
            'fecha' => date('Y-m-d'),
        ];
    }

    public function nucleoFamiliar()
    {
        $legacy = new LegacyDatabaseService('comfaca');

        $trabajador = $legacy->select('SELECT subsi15.*,
        CASE
            WHEN subsi15.estado = "A" THEN "ACTIVO"
            WHEN subsi15.estado = "B" THEN "INACTIVO"
            WHEN subsi15.estado = "M" THEN "MUERTO"
            ELSE "OTRO"
        END as estado_detalle,
        CONCAT_WS(" ", subsi15.prinom, subsi15.segnom, subsi15.priape, subsi15.segape) as nomtra, 
        subsi02.razsoc
        FROM subsi15 
        LEFT JOIN subsi02 ON subsi02.nit = subsi15.nit 
        WHERE subsi15.cedtra = ?', [$this->cedtra]);

        $conyuges = $legacy->select('SELECT subsi20.*, subsi21.fecafi, subsi21.comper,
        CONCAT_WS(" ", subsi20.prinom, subsi20.segnom, subsi20.priape, subsi20.segape) as nomcony  
        FROM subsi21 
        INNER JOIN subsi20 ON subsi20.cedcon = subsi21.cedcon 
        WHERE subsi21.cedtra = ? AND subsi21.comper = ? AND subsi20.estado = ?', [$this->cedtra, 'S', 'A']);

        $beneficiarios = $legacy->select('SELECT subsi22.*,
        CONCAT_WS(" ", subsi22.prinom, subsi22.segnom, subsi22.priape, subsi22.segape) as nomben,
        subsi23.fecafi,   
        CASE
            WHEN subsi22.parent = "1" THEN "HIJO"
            WHEN subsi22.parent = "2" THEN "HERMANO"
            WHEN subsi22.parent = "3" THEN "PADRES"
            ELSE "OTRO"
        END as parent_detalle
        FROM subsi23 
        INNER JOIN subsi22 ON subsi22.codben = subsi23.codben 
        WHERE subsi23.cedtra = ? AND subsi22.estado = ?', [$this->cedtra, 'A']);

        $legacy->disconnect();
        return [
            'trabajador' => (object) $trabajador->first(),
            'conyuges' => $conyuges->map(fn($c) => (object) $c)->toArray(),
            'beneficiarios' => $beneficiarios->map(fn($b) => (object) $b)->toArray(),
            'fecha' => date('Y-m-d'),
        ];
    }

    public function multiAfiliacion()
    {
        $legacy = new LegacyDatabaseService('comfaca');

        $trabajador = $legacy->select('SELECT subsi15.*,
        CASE 
            WHEN subsi15.estado = "A" THEN "ACTIVO"
            WHEN subsi15.estado = "B" THEN "INACTIVO"
            WHEN subsi15.estado = "M" THEN "MUERTO"
            ELSE "OTRO"
        END as estado_detalle,
        CONCAT_WS(" ", subsi15.prinom, subsi15.segnom, subsi15.priape, subsi15.segape) as nomtra, 
        subsi02.razsoc 
        FROM subsi15 
        LEFT JOIN subsi02 ON subsi02.nit = subsi15.nit 
        WHERE subsi15.cedtra = ?', [$this->cedtra]);

        $multiAfiliacion = $legacy->select("SELECT 
            s168.*,
            IF(s168.agro = 'S', 'SI', 'NO') as agro_detalle,
            s48.detalle as 'codsuc_detalle', 
            s73.detalle as 'codlis_detalle',
            gener09.detzon as codzon_detalle,
            (CASE WHEN s168.estado='A' THEN 'ACTIVO'  
                WHEN s168.estado='I' THEN 'INACTIVO' 
                WHEN s168.estado='M' THEN 'MUERTO' 
                ELSE '' 
            END) as estado_detalle, 
            IF(s168.tipcon = 'F', 'FIJO', 'INDEFINIDO') as tipcon_detalle,
            subsi71.detalle as tipcot_detalle, 
             (CASE WHEN s168.tipjor='C' THEN 'COMPLETA' 
                WHEN s168.tipjor='M' THEN 'MEDIA' 
                WHEN s168.tipjor='P' THEN 'PARCIAL' 
                ELSE '' 
            END) as tipjor_detalle, 
            IF(s168.giro2 IS NULL, 'N', s168.giro2) as giro2, 
            IF(s168.codgir2 IS NULL, 'N', s168.codgir2) as codgir2
            FROM subsi168 s168
            LEFT JOIN subsi48 s48 ON s48.nit = s168.nit and s48.codsuc = s168.codsuc 
            LEFT JOIN subsi73 s73 ON s73.nit = s168.nit and s73.codlis = s168.codlis 
            LEFT JOIN gener09 ON gener09.codzon = s168.codzon 
            LEFT JOIN subsi71 ON subsi71.tipcot = s168.tipcot 
            WHERE s168.cedtra =? AND s168.estado = ?", [$this->cedtra, 'A']);

        $legacy->disconnect();
        return [
            'trabajador' => (object) $trabajador->first(),
            'multiAfiliacion' => $multiAfiliacion->map(fn($m) => (object) $m)->toArray(),
            'fecha' => date('Y-m-d'),
        ];
    }

    public function trabajadorPlanillas()
    {
        $legacy = new LegacyDatabaseService('comfaca');

        $trabajador = $legacy->select('SELECT subsi15.*,
        CASE
            WHEN subsi15.estado = "A" THEN "ACTIVO"
            WHEN subsi15.estado = "B" THEN "INACTIVO"
            WHEN subsi15.estado = "M" THEN "MUERTO"
            ELSE "OTRO"
        END as estado_detalle,
        CONCAT_WS(" ", subsi15.prinom, subsi15.segnom, subsi15.priape, subsi15.segape) as nomtra, 
        subsi02.razsoc
        FROM subsi15 
        LEFT JOIN subsi02 ON subsi02.nit = subsi15.nit 
        WHERE subsi15.cedtra = ?', [$this->cedtra]);

        $empresasAportes = $legacy->select("SELECT 
			subsi64.nitpla, 
			subsi64.nit, 
			subsi64.digver  
			FROM subsi65 
			INNER JOIN subsi64 ON subsi65.numero = subsi64.numero  
			WHERE subsi65.cedtra = ? 
			GROUP BY 1, 2 
			ORDER BY periodo, fecrec DESC", [$this->cedtra]);

        $devoluciones = $legacy->select("SELECT subsi196.*, 
            subsi64.nit, 
            subsi64.digver, 
            subsi64.fecrec,
            subsi65.valnom, 
            subsi64.perapo,
            subsi65.valapo,
            subsi65.salbas,
            subsi65.diatra   
            FROM subsi196 
            INNER JOIN subsi64 ON subsi196.marca=subsi64.marca and subsi196.documento=subsi64.documento 
            INNER JOIN subsi65 ON subsi196.cedtra=subsi65.cedtra and subsi64.numero=subsi65.numero
            WHERE subsi196.cedtra=? 
            GROUP BY subsi65.cedtra, subsi64.nit, subsi196.marca, subsi196.documento 
            ORDER BY subsi196.fecpag", [$this->cedtra]);


        $aportesPlanilla = [];
        foreach ($empresasAportes->toArray() as $aportes) {
            $query =  $legacy->select("SELECT 
                subsi64.tippla,   
                subsi65.valnom, 
                subsi65.valapo, 
                subsi64.perapo,
                subsi64.fecrec,
                subsi65.diatra, 
                subsi65.ingtra, 
                subsi65.novret,
                subsi65.vacnom,    
                subsi65.novitg,
                subsi65.licnom,
                subsi65.novstc,
                subsi65.incnom 
                FROM subsi65 
                INNER JOIN subsi64 ON subsi64.numero = subsi65.numero 
                WHERE 
                subsi65.cedtra = ? AND 
                (subsi64.nitpla = ? OR subsi64.nit = ?) 
                ORDER BY subsi64.perapo DESC
			", [
                $this->cedtra,
                $aportes['nitpla'],
                $aportes['nit']
            ]);

            foreach ($query->toArray() as $item) {
                if ($item['valapo'] == 0 || $item['valapo'] == '') {
                    $item['diatra'] = 0;
                }
                $item['horas'] = $item['diatra'] * 8;
                $aportesPlanilla[] = (object) $item;
            }
        }

        $legacy->disconnect();
        return [
            'trabajador' => $trabajador->isNotEmpty() ? (object) $trabajador->first() : null,
            'devoluciones' => $devoluciones->map(fn($d) => (object) $d)->toArray(),
            'aportesPlanilla' => $aportesPlanilla,
            'fecha' => date('Y-m-d'),
        ];
    }
}
