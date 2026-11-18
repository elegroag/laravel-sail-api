<?php

namespace App\Console\Commands;

use App\Models\Gener40;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignGener40RoutesCommand extends Command
{
    protected $signature = 'cajas:gener40-assign-routes
                            {--dry-run : Solo muestra el resumen sin guardar}';

    protected $description = 'Asigna controller y action a gener40 desde un mapa embebido (sin CSV; omite FIRMAS/PREGUNTAS/SERVICIO DOMESTICO al no incluirlos)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $map = $this->builtinMapping();

        $stats = [
            'mapa' => count($map),
            'no_encontradas' => 0,
            'sin_cambios' => 0,
            'actualizadas' => 0,
        ];
        $cambios = [];

        DB::beginTransaction();
        try {
            foreach ($map as $codigo => $route) {
                $item = Gener40::query()->where('codigo', $codigo)->first();
                if (! $item) {
                    $stats['no_encontradas']++;
                    $this->warn("Código no encontrado en gener40: {$codigo}");

                    continue;
                }

                $payload = [];
                if ((string) $item->controller !== $route['controller']) {
                    $payload['controller'] = $route['controller'];
                }
                if ((string) $item->action !== $route['action']) {
                    $payload['action'] = $route['action'];
                }

                if ($payload === []) {
                    $stats['sin_cambios']++;

                    continue;
                }

                $cambios[] = [
                    $codigo,
                    (string) ($item->detalle ?? ''),
                    $payload['controller'] ?? (string) ($item->controller ?? '—'),
                    $payload['action'] ?? (string) ($item->action ?? '—'),
                ];

                if (! $dryRun) {
                    $item->fill($payload);
                    $item->save();
                }

                $stats['actualizadas']++;
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Error al asignar rutas: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($cambios !== []) {
            $this->table(['Código', 'Detalle', 'Controller', 'Action'], $cambios);
        }

        $this->newLine();
        $this->info($dryRun ? 'Simulación (dry-run) completada.' : 'Asignación completada.');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Entradas en mapa', $stats['mapa']],
                ['Código no encontrado', $stats['no_encontradas']],
                ['Sin cambios', $stats['sin_cambios']],
                [$dryRun ? 'A actualizar' : 'Actualizadas', $stats['actualizadas']],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Mapa codigo → controller/action. No incluye FIRMAS, PREGUNTAS ni SERVICIO DOMESTICO.
     *
     * @return array<string, array{controller: string, action: string}>
     */
    private function builtinMapping(): array
    {
        $rows = [
            // BASICA → Mercurio01
            '1' => ['Mercurio01Controller', 'index'],
            '2' => ['Mercurio01Controller', 'update'],
            '3' => ['Mercurio01Controller', 'store'],
            '4' => ['Mercurio01Controller', 'index'],

            // DATOS DE LA CAJA → Mercurio02
            '5' => ['Mercurio02Controller', 'index'],
            '6' => ['Mercurio02Controller', 'update'],
            '7' => ['Mercurio02Controller', 'store'],
            '8' => ['Mercurio02Controller', 'index'],

            // TIPOS DE ACCESO → Mercurio06
            '14' => ['Mercurio06Controller', 'index'],
            '15' => ['Mercurio06Controller', 'update'],
            '16' => ['Mercurio06Controller', 'store'],
            '17' => ['Mercurio06Controller', 'index'],
            '18' => ['Mercurio06Controller', 'destroy'],
            '19' => ['Mercurio06Controller', 'index'],
            '20' => ['Mercurio06Controller', 'updateCampo'],
            '21' => ['Mercurio06Controller', 'destroyCampo'],

            // DOCUMENTOS → Mercurio12
            '22' => ['Mercurio12Controller', 'index'],
            '23' => ['Mercurio12Controller', 'update'],
            '24' => ['Mercurio12Controller', 'store'],
            '25' => ['Mercurio12Controller', 'index'],
            '26' => ['Mercurio12Controller', 'destroy'],

            // TIPOS OPCIONES → Mercurio09
            '27' => ['Mercurio09Controller', 'index'],
            '28' => ['Mercurio09Controller', 'update'],
            '29' => ['Mercurio09Controller', 'store'],
            '30' => ['Mercurio09Controller', 'index'],
            '31' => ['Mercurio09Controller', 'destroy'],

            // PERMISOS → Gener42
            '32' => ['Gener42Controller', 'assign'],
            '33' => ['Gener42Controller', 'index'],

            // MOTIVOS RECHAZO → Mercurio11
            '44' => ['Mercurio11Controller', 'index'],
            '45' => ['Mercurio11Controller', 'update'],
            '46' => ['Mercurio11Controller', 'store'],
            '47' => ['Mercurio11Controller', 'index'],
            '48' => ['Mercurio11Controller', 'destroy'],

            // OFICINAS → Mercurio04
            '49' => ['Mercurio04Controller', 'index'],
            '50' => ['Mercurio04Controller', 'update'],
            '51' => ['Mercurio04Controller', 'store'],
            '52' => ['Mercurio04Controller', 'index'],
            '53' => ['Mercurio04Controller', 'destroy'],

            // GALERIA → Mercurio26
            '54' => ['Mercurio26Controller', 'index'],
            '55' => ['Mercurio26Controller', 'moveUp'],
            '56' => ['Mercurio26Controller', 'moveDown'],
            '57' => ['Mercurio26Controller', 'store'],
            '58' => ['Mercurio26Controller', 'destroy'],

            // APROBAR EMPRESA
            '59' => ['ApruebaEmpresaController', 'index'],
            '60' => ['ApruebaEmpresaController', 'show'],
            '61' => ['ApruebaEmpresaController', 'index'],
            '62' => ['ApruebaEmpresaController', 'aprobar'],
            '63' => ['ApruebaEmpresaController', 'devolver'],
            '64' => ['ApruebaEmpresaController', 'rechazar'],

            // FACULTATIVOS
            '65' => ['ApruebaFacultativoController', 'index'],
            '66' => ['ApruebaFacultativoController', 'show'],
            '67' => ['ApruebaFacultativoController', 'index'],
            '68' => ['ApruebaFacultativoController', 'aprobar'],
            '69' => ['ApruebaFacultativoController', 'devolver'],
            '70' => ['ApruebaFacultativoController', 'rechazar'],

            // PENSIONADOS
            '71' => ['ApruebaPensionadoController', 'index'],
            '72' => ['ApruebaPensionadoController', 'show'],
            '73' => ['ApruebaPensionadoController', 'index'],
            '74' => ['ApruebaPensionadoController', 'aprobar'],
            '75' => ['ApruebaPensionadoController', 'devolver'],
            '76' => ['ApruebaPensionadoController', 'rechazar'],

            // MADRES COMUNITARIAS (rutas legacy)
            '77' => ['ApruebaComunitariaController', 'index'],
            '78' => ['ApruebaComunitariaController', 'infor'],
            '79' => ['ApruebaComunitariaController', 'buscar'],
            '80' => ['ApruebaComunitariaController', 'aprueba'],
            '81' => ['ApruebaComunitariaController', 'devolver'],
            '82' => ['ApruebaComunitariaController', 'rechazar'],

            // TRABAJADORES
            '89' => ['ApruebaTrabajadorController', 'index'],
            '90' => ['ApruebaTrabajadorController', 'show'],
            '91' => ['ApruebaTrabajadorController', 'index'],
            '92' => ['ApruebaTrabajadorController', 'aprobar'],
            '93' => ['ApruebaTrabajadorController', 'devolver'],
            '94' => ['ApruebaTrabajadorController', 'rechazar'],

            // RETIRO (rutas legacy / sin archivo de rutas aún)
            '95' => ['ApruebaRetiroController', 'index'],
            '96' => ['ApruebaRetiroController', 'infor'],
            '97' => ['ApruebaRetiroController', 'buscar'],
            '98' => ['ApruebaRetiroController', 'aprobar'],
            '99' => ['ApruebaRetiroController', 'devolver'],
            '100' => ['ApruebaRetiroController', 'rechazar'],

            // CONYUGE
            '101' => ['ApruebaConyugeController', 'index'],
            '102' => ['ApruebaConyugeController', 'show'],
            '103' => ['ApruebaConyugeController', 'index'],
            '104' => ['ApruebaConyugeController', 'aprobar'],
            '105' => ['ApruebaConyugeController', 'devolver'],
            '106' => ['ApruebaConyugeController', 'rechazar'],

            // BENEFICIARIO
            '107' => ['ApruebaBeneficiarioController', 'index'],
            '108' => ['ApruebaBeneficiarioController', 'show'],
            '109' => ['ApruebaBeneficiarioController', 'index'],
            '110' => ['ApruebaBeneficiarioController', 'aprobar'],
            '111' => ['ApruebaBeneficiarioController', 'devolver'],
            '112' => ['ApruebaBeneficiarioController', 'rechazar'],

            // CERTIFICADOS
            '113' => ['ApruebaCertificadoController', 'index'],
            '114' => ['ApruebaCertificadoController', 'show'],
            '115' => ['ApruebaCertificadoController', 'index'],
            '116' => ['ApruebaCertificadoController', 'aprobar'],
            '117' => ['ApruebaCertificadoController', 'devolver'],
            '118' => ['ApruebaCertificadoController', 'rechazar'],

            // DATOS BASICOS TRABAJADOR
            '119' => ['ApruebaUpTrabajadorController', 'index'],
            '120' => ['ApruebaUpTrabajadorController', 'show'],
            '121' => ['ApruebaUpTrabajadorController', 'index'],
            '122' => ['ApruebaUpTrabajadorController', 'aprobar'],
            '123' => ['ApruebaUpTrabajadorController', 'devolver'],
            '124' => ['ApruebaUpTrabajadorController', 'rechazar'],

            // DATOS BASICOS EMPRESA
            '131' => ['ApruebaUpEmpresaController', 'index'],
            '132' => ['ApruebaUpEmpresaController', 'show'],
            '133' => ['ApruebaUpEmpresaController', 'index'],
            '134' => ['ApruebaUpEmpresaController', 'aprobar'],
            '135' => ['ApruebaUpEmpresaController', 'devolver'],
            '136' => ['ApruebaUpEmpresaController', 'rechazar'],

            // Consultas / reasignación
            '137' => ['ConsultaController', 'index'],
            '138' => ['ConsultaController', 'cargaLaboral'],
            '139' => ['ConsultaController', 'indicadores'],
            '140' => ['ReasignaController', 'index'],
        ];

        $map = [];
        foreach ($rows as $codigo => [$controller, $action]) {
            $map[(string) $codigo] = [
                'controller' => $controller,
                'action' => $action,
            ];
        }

        return $map;
    }
}
