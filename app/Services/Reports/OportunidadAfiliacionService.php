<?php

namespace App\Services\Reports;

use App\Models\Gener02;
use App\Models\Mercurio10;
use App\Models\Mercurio31;
use App\Services\LegacyDatabaseService;
use App\Support\AfiliacionNormalizer;
use App\Support\AuditoriaSolicitudResolver;
use App\Support\DiasHabilesCalculator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class OportunidadAfiliacionService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDataset(array $filtros = []): array
    {
        $tipos = $this->resolveTipos($filtros['tipafis'] ?? null);
        $fecini = $filtros['fecini'] ?? null;
        $fecfin = $filtros['fecfin'] ?? null;
        $umbral = (int) config('reportes.oportunidad_umbral_dias', 3);

        if ($tipos === [] || ! $fecini || ! $fecfin) {
            return [];
        }

        $tipopcKeys = array_map('strval', array_keys($tipos));

        $eventosP = Mercurio10::query()
            ->where('estado', 'P')
            ->whereIn('tipopc', $tipopcKeys)
            ->whereBetween('fecsis', [$fecini, $fecfin])
            ->orderBy('fecsis')
            ->orderBy('tipopc')
            ->orderBy('numero')
            ->orderBy('item')
            ->get();

        if ($eventosP->isEmpty()) {
            return [];
        }

        $solicitudesIndex = $this->buildSolicitudesIndex($eventosP, $tipos);
        $usuariosIndex = $this->buildUsuariosIndex($solicitudesIndex);
        $titularesIndex = $this->buildTitularesIndex();
        $tipdocIndex = $this->buildTipdocIndex();
        $dataset = [];
        $hoy = Carbon::today()->format('Y-m-d');

        foreach ($eventosP as $evento) {
            $tipopc = (int) $evento->tipopc;
            $config = $tipos[$tipopc] ?? null;
            if ($config === null) {
                continue;
            }

            $solicitudKey = $this->pairKey((string) $evento->tipopc, (int) $evento->numero);
            $solicitud = $solicitudesIndex[$solicitudKey] ?? null;
            if ($solicitud === null) {
                continue;
            }

            $estadoSolicitud = strtoupper(trim((string) ($solicitud->estado ?? '')));
            if ($estadoSolicitud === 'I') {
                continue;
            }

            $fechaInicio = $this->formatFecha($evento->fecsis);
            $fechaCierre = $this->formatFecha($evento->feccie);
            $fechaFinEfectiva = $fechaCierre ?? $hoy;

            $record = AfiliacionNormalizer::normalize($solicitud, $tipopc, $config, $titularesIndex);
            $record = $this->enrichIdentificacion($record, $tipdocIndex);

            $usuario = trim((string) ($solicitud->usuario ?? ''));
            $record['usuario'] = $usuario;
            $record['nombre_usuario'] = $usuario !== ''
                ? (string) ($usuariosIndex[$usuario] ?? '')
                : '';

            $record['ruuid'] = $evento->ruuid ?: '';
            $record['item'] = $evento->item;
            $record['fecsol'] = $fechaInicio;
            $record['fecapr'] = $fechaCierre;
            $record['fecha_cierre'] = $fechaCierre;
            $record['dias_habiles'] = DiasHabilesCalculator::between($fechaInicio, $fechaCierre);
            $record['estado_oportunidad'] = $this->resolverEstadoOportunidad(
                $fechaFinEfectiva,
                $record['dias_habiles'],
                $umbral
            );

            // Estado = solicitud (no el evento Mercurio10 P).
            // Snapshots en auditoria_* se etiquetan como Archivada (no "Borrada").
            $record['estado'] = AuditoriaSolicitudResolver::isArchivado($solicitud)
                ? 'Archivada'
                : (string) ($record['estado'] ?? '');
            $estadoSolicitud = strtoupper((string) ($record['estado_codigo'] ?? $solicitud->estado ?? ''));
            $radicadoCerrado = strtoupper((string) ($evento->cerrada ?? 'N')) === 'S'
                || $estadoSolicitud === 'A';
            $record['estado_radicado'] = $radicadoCerrado ? 'Cerrado' : 'Enviado';

            $dataset[] = $record;
        }

        usort($dataset, function (array $a, array $b): int {
            $fechaCompare = strcmp((string) ($a['fecsol'] ?? ''), (string) ($b['fecsol'] ?? ''));
            if ($fechaCompare !== 0) {
                return $fechaCompare;
            }

            $idCompare = ((int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0));
            if ($idCompare !== 0) {
                return $idCompare;
            }

            return (int) ($a['item'] ?? 0) <=> (int) ($b['item'] ?? 0);
        });

        return $dataset;
    }

    /**
     * @return array{total: int, en_termino: int, vencido: int, en_tramite: int}
     */
    public function buildResumen(array $filtros = []): array
    {
        $dataset = $this->buildDataset($filtros);

        $resumen = [
            'total' => count($dataset),
            'en_termino' => 0,
            'vencido' => 0,
            'en_tramite' => 0,
        ];

        foreach ($dataset as $row) {
            $estado = $row['estado_oportunidad'] ?? '';
            match ($estado) {
                'EN_TERMINO' => $resumen['en_termino']++,
                'VENCIDO' => $resumen['vencido']++,
                'EN_TRAMITE' => $resumen['en_tramite']++,
                default => null,
            };
        }

        return $resumen;
    }

    /**
     * Solicitudes padre indexadas por tipopc|numero.
     * Si no existe en la tabla viva, usa el snapshot más reciente en auditoria_*.
     *
     * @param  Collection<int, Mercurio10>  $eventosP
     * @param  array<int, array<string, mixed>>  $tipos
     * @return array<string, object>
     */
    private function buildSolicitudesIndex(Collection $eventosP, array $tipos): array
    {
        $index = [];

        $grouped = $eventosP->groupBy(fn (Mercurio10 $e) => (int) $e->tipopc);

        foreach ($grouped as $tipopc => $events) {
            $config = $tipos[(int) $tipopc] ?? null;
            if ($config === null || empty($config['model'])) {
                continue;
            }

            $ids = $events->pluck('numero')->map(fn ($n) => (int) $n)->unique()->values()->all();
            if ($ids === []) {
                continue;
            }

            $modelClass = $config['model'];
            $solicitudes = $modelClass::query()->whereIn('id', $ids)->get();

            foreach ($solicitudes as $solicitud) {
                $key = $this->pairKey((string) $tipopc, (int) $solicitud->id);
                $index[$key] = $solicitud;
            }

            $faltantes = array_values(array_diff($ids, $solicitudes->pluck('id')->map(fn ($id) => (int) $id)->all()));
            if ($faltantes === []) {
                continue;
            }

            $auditModel = $config['audit_model']
                ?? (config('reportes.solicitud_auditoria')[(int) $tipopc]['audit_model'] ?? null);

            if ($auditModel === null || ! class_exists($auditModel)) {
                continue;
            }

            $auditRows = $auditModel::query()
                ->whereIn('id', $faltantes)
                ->orderByDesc('deleted_at')
                ->orderByDesc('audit_id')
                ->get();

            $seen = [];
            foreach ($auditRows as $row) {
                $id = (int) $row->id;
                if (isset($seen[$id])) {
                    continue;
                }
                $seen[$id] = true;
                $key = $this->pairKey((string) $tipopc, $id);
                $index[$key] = $row;
            }
        }

        return $index;
    }

    /**
     * Índice gener02: usuario => nombre.
     *
     * @param  array<string, object>  $solicitudesIndex
     * @return array<string, string>
     */
    private function buildUsuariosIndex(array $solicitudesIndex): array
    {
        $usuarios = collect($solicitudesIndex)
            ->map(fn ($solicitud) => trim((string) ($solicitud->usuario ?? '')))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($usuarios === []) {
            return [];
        }

        return Gener02::query()
            ->whereIn('usuario', $usuarios)
            ->get(['usuario', 'nombre'])
            ->mapWithKeys(fn (Gener02 $asesor): array => [
                (string) $asesor->usuario => trim((string) $asesor->nombre),
            ])
            ->all();
    }

    private function pairKey(string $tipopc, int $numero): string
    {
        return $tipopc.'|'.$numero;
    }

    private function formatFecha(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '0000-00-00') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildTitularesIndex(): array
    {
        return Mercurio31::query()
            ->select(['cedtra', 'priape', 'segape', 'prinom', 'segnom'])
            ->get()
            ->mapWithKeys(function (Mercurio31 $trabajador): array {
                $cedtra = trim((string) $trabajador->getCedtra());
                if ($cedtra === '') {
                    return [];
                }

                $nombre = trim(implode(' ', array_filter([
                    $trabajador->getPriape(),
                    $trabajador->getSegape(),
                    $trabajador->getPrinom(),
                    $trabajador->getSegnom(),
                ])));

                return [$cedtra => $nombre];
            })
            ->all();
    }

    /**
     * Catálogo de tipos de documento desde gener18 (legacy comfaca).
     *
     * @return array<string, string>
     */
    private function buildTipdocIndex(): array
    {
        try {
            $legacy = new LegacyDatabaseService('comfaca');
            $rows = $legacy->select('SELECT coddoc, detdoc, codrua FROM gener18');
            $legacy->disconnect();
        } catch (Throwable) {
            return [];
        }

        $index = [];
        foreach ($rows as $row) {
            $coddoc = trim((string) ($row['coddoc'] ?? ''));
            $codrua = trim((string) ($row['codrua'] ?? ''));
            $detdoc = trim((string) ($row['detdoc'] ?? ''));
            $label = $codrua !== '' ? $codrua : ($detdoc !== '' ? $detdoc : $coddoc);

            if ($coddoc !== '') {
                $index[$coddoc] = $label;
            }

            if ($codrua !== '') {
                $index[$codrua] = $label;
                $index[strtoupper($codrua)] = $label;
            }
        }

        return $index;
    }

    /**
     * @param  array<string, mixed>  $record
     * @param  array<string, string>  $tipdocIndex
     * @return array<string, mixed>
     */
    private function enrichIdentificacion(array $record, array $tipdocIndex): array
    {
        $tipdocCode = trim((string) ($record['tipdoc'] ?? ''));
        $numero = trim((string) ($record['documento'] ?? ''));

        $tipo = $tipdocIndex[$tipdocCode]
            ?? $tipdocIndex[strtoupper($tipdocCode)]
            ?? $tipdocCode;

        $record['tipo_documento'] = $tipo;
        $record['numero_identificacion'] = $numero;
        $record['tipo_identificacion'] = trim($tipo.' '.$numero);

        return $record;
    }

    private function resolverEstadoOportunidad(?string $fechaFinEfectiva, ?int $diasHabiles, int $umbral): string
    {
        if ($diasHabiles === null) {
            return 'EN_TRAMITE';
        }

        // fechaFinEfectiva siempre viene informada (feccie o hoy) para alinear con días calculados.
        if ($fechaFinEfectiva === null || $fechaFinEfectiva === '' || $fechaFinEfectiva === '0000-00-00') {
            return $diasHabiles > $umbral ? 'VENCIDO' : 'EN_TRAMITE';
        }

        return $diasHabiles > $umbral ? 'VENCIDO' : 'EN_TERMINO';
    }

    /**
     * @param  array<int|string>|string|int|null  $tipafis
     * @return array<int, array<string, mixed>>
     */
    private function resolveTipos(array|string|int|null $tipafis): array
    {
        $tipos = config('reportes.oportunidad_tipos', []);

        if ($tipafis === null || $tipafis === [] || $tipafis === '') {
            return $tipos;
        }

        if (is_int($tipafis) || is_string($tipafis)) {
            $tipafis = array_filter(array_map('trim', explode(',', (string) $tipafis)), static fn ($v) => $v !== '');
        }

        $selected = [];
        foreach ($tipafis as $tipopc) {
            $key = (int) $tipopc;
            if (isset($tipos[$key])) {
                $selected[$key] = $tipos[$key];
            }
        }

        return $selected !== [] ? $selected : $tipos;
    }
}
