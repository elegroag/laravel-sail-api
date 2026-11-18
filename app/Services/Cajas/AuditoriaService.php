<?php

namespace App\Services\Cajas;

use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio10;
use App\Models\Mercurio33;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\GeneralService;
use App\Support\AuditoriaSolicitudFieldsBuilder;
use App\Support\AuditoriaSolicitudResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AuditoriaService
{
    /** @var array<string, string>|null */
    private ?array $camposActualizacion = null;

    /**
     * @return list<array{value: string, label: string}>
     */
    public function tipopcOptions(): array
    {
        return Mercurio09::query()
            ->orderBy('tipopc')
            ->get(['tipopc', 'detalle'])
            ->map(fn (Mercurio09 $row) => [
                'value' => (string) $row->tipopc,
                'label' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{
     *   data: list<array<string, mixed>>,
     *   hasExtra: bool,
     *   total: int,
     *   columns: list<string>
     * }
     */
    public function consultar(string $tipopc, string $fecini, string $fecfin): array
    {
        $this->assertFechas($fecini, $fecfin);

        $condi = "mercurio10.fecsis>='{$fecini}' and mercurio10.fecsis<='{$fecfin}'";
        $mercurio = (new GeneralService)->consultaTipopc($tipopc, 'auditoria', '', '', $condi);

        $hasExtra = in_array($tipopc, ['8', '5'], true);
        $datos = $this->normalizeDatos($mercurio['datos'] ?? []);

        $responsables = $this->mapResponsables($datos);

        $result = [];
        foreach ($datos as $mmercurio) {
            $id = $mmercurio->getId();
            $usuario = (string) $mmercurio->getUsuario();

            $result[] = [
                'id' => $id,
                'documento' => $this->getDocumento($mmercurio, $tipopc),
                'nombre' => $this->getNombre($mmercurio, $tipopc),
                'responsable' => $responsables[$usuario] ?? '',
                'fecha' => Carbon::parse($mmercurio->getFecest())->format('Y-m-d'),
                'fecsol' => $this->formatFecha($mmercurio, 'fecsol'),
                'fecapr' => $mmercurio->getEstado() === 'A'
                    ? ($this->formatFecha($mmercurio, 'fecapr') ?? Carbon::parse($mmercurio->getFecest())->format('Y-m-d'))
                    : '',
                'radicado' => $mmercurio->ruuid ?? null,
                'dias_vencidos' => CalculatorDias::calcular($tipopc, $id),
                'extra' => $hasExtra ? $this->getExtra($mmercurio, $tipopc) : null,
                'estado' => $mmercurio->getEstadoDetalle(),
                'estado_tipo' => $mmercurio->getEstado(),
            ];
        }

        $columns = ['Documento', 'Nombre', 'Responsable', 'Fecha', 'Fecsol', 'Fecapr', 'Radicado', 'Días'];
        if ($hasExtra) {
            $columns[] = 'Extra';
        }
        $columns[] = 'Estado';
        $columns[] = 'Acciones';

        return [
            'data' => $result,
            'hasExtra' => $hasExtra,
            'total' => count($result),
            'columns' => $columns,
        ];
    }

    /**
     * @return array{headers: list<string>, rows: list<list<mixed>>}
     */
    public function buildReporteRows(string $tipopc, string $fecini, string $fecfin): array
    {
        $consulta = $this->consultar($tipopc, $fecini, $fecfin);
        $headers = array_values(array_filter(
            $consulta['columns'],
            fn (string $column) => $column !== 'Acciones'
        ));

        $rows = [];
        foreach ($consulta['data'] as $row) {
            $fila = [
                $row['documento'],
                $row['nombre'],
                $row['responsable'],
                $row['fecha'],
                $row['fecsol'],
                $row['fecapr'],
                $row['radicado'] ?? '',
                $row['dias_vencidos'],
            ];
            if ($consulta['hasExtra']) {
                $fila[] = $row['extra'] ?? '';
            }
            $fila[] = $row['estado'];
            $rows[] = $fila;
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * @return array{
     *   tipopc: string,
     *   tipopc_label: string,
     *   id: int,
     *   fields: list<array{label: string, value: string}>,
     *   extraFields: list<array{label: string, value: string}>,
     *   seguimiento: list<array{nota: string, estado: string, fecha: string}>
     * }
     */
    public function detalle(string $tipopc, int $id): array
    {
        $solicitud = AuditoriaSolicitudResolver::resolve($tipopc, $id);
        if ($solicitud === null) {
            throw ValidationException::withMessages([
                'id' => 'Solicitud no encontrada',
            ]);
        }

        $hasExtra = in_array($tipopc, ['5', '8'], true);
        $tipoDetalle = (string) (Mercurio09::where('tipopc', $tipopc)->value('detalle') ?? $tipopc);

        $fieldsMap = [
            'Tipo de opción' => $tipoDetalle,
            'ID' => (string) $solicitud->getId(),
            'Documento' => $this->getDocumento($solicitud, $tipopc),
            'Nombre' => $this->getNombre($solicitud, $tipopc),
            'Responsable' => $this->getResponsable($solicitud),
            'Estado' => method_exists($solicitud, 'getEstadoDetalle')
                ? (string) $solicitud->getEstadoDetalle()
                : (string) ($solicitud->estado ?? ''),
            'Fecha solicitud' => $this->formatFecha($solicitud, 'fecsol') ?? '',
            'Fecha estado' => $this->formatFecha($solicitud, 'fecest') ?? '',
            'Fecha aprobación' => $this->formatFecha($solicitud, 'fecapr') ?? '',
            'Radicado' => (string) ($solicitud->ruuid ?? ''),
            'Días vencidos' => (string) CalculatorDias::calcular($tipopc, $id),
        ];

        if ($hasExtra) {
            $fieldsMap['Extra'] = $this->getExtra($solicitud, $tipopc);
        }

        $extraMap = (new AuditoriaSolicitudFieldsBuilder)->build($tipopc, $solicitud);

        return [
            'tipopc' => $tipopc,
            'tipopc_label' => $tipoDetalle,
            'id' => $id,
            'fields' => $this->mapToLabelValue($fieldsMap),
            'extraFields' => $this->mapToLabelValue($extraMap),
            'seguimiento' => $this->seguimientoRows($tipopc, $id),
        ];
    }

    /**
     * @param  array<string, mixed>  $map
     * @return list<array{label: string, value: string}>
     */
    private function mapToLabelValue(array $map): array
    {
        $out = [];
        foreach ($map as $label => $value) {
            if (is_bool($value)) {
                $text = $value ? 'Sí' : 'No';
            } elseif (is_array($value)) {
                $text = implode(', ', array_map(fn ($item) => (string) $item, $value));
            } else {
                $text = trim((string) ($value ?? ''));
            }

            $out[] = [
                'label' => (string) $label,
                'value' => $text,
            ];
        }

        return $out;
    }

    /**
     * @return list<array{nota: string, estado: string, fecha: string}>
     */
    private function seguimientoRows(string $tipopc, int $id): array
    {
        $rows = Mercurio10::query()
            ->where('tipopc', $tipopc)
            ->where('numero', $id)
            ->orderBy('item')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        return $rows->map(fn (Mercurio10 $row) => [
            'nota' => strip_tags(strtolower((string) $row->getNota())),
            'estado' => (string) $row->getDetalleEstado(),
            'fecha' => (string) $row->getFecsis(),
        ])->values()->all();
    }

    private function assertFechas(string $fecini, string $fecfin): void
    {
        try {
            $ini = Carbon::createFromFormat('Y-m-d', $fecini)->startOfDay();
            $fin = Carbon::createFromFormat('Y-m-d', $fecfin)->startOfDay();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'fecini' => 'Las fechas deben tener formato YYYY-MM-DD.',
            ]);
        }

        if ($fin->lt($ini)) {
            throw ValidationException::withMessages([
                'fecfin' => 'La fecha final debe ser mayor o igual a la inicial.',
            ]);
        }
    }

    /**
     * @return list<object>
     */
    private function normalizeDatos(mixed $datos): array
    {
        if ($datos instanceof Paginator) {
            return $datos->items();
        }

        if ($datos instanceof Collection) {
            return $datos->all();
        }

        return is_array($datos) ? $datos : [];
    }

    /**
     * @param  list<object>  $datos
     * @return array<string, string>
     */
    private function mapResponsables(array $datos): array
    {
        $usuarios = collect($datos)
            ->map(fn ($row) => method_exists($row, 'getUsuario') ? (string) $row->getUsuario() : '')
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
            ->mapWithKeys(fn (Gener02 $row) => [
                (string) $row->usuario => (string) $row->getNombre(),
            ])
            ->all();
    }

    private function getDocumento(object $mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '1', '9', '10', '11', '12', '13' => $this->callGetter($mmercurio, 'getCedtra'),
            '2' => $this->callGetter($mmercurio, 'getNit'),
            '3' => $this->callGetter($mmercurio, 'getCedcon'),
            '4' => $this->callGetter($mmercurio, 'getNumdoc'),
            default => $this->callGetter($mmercurio, 'getDocumento'),
        };
    }

    private function getNombre(object $mmercurio, string $tipopc): string
    {
        return match ($tipopc) {
            '2' => $this->callGetter($mmercurio, 'getRazsoc'),
            '5' => $this->callGetter($mmercurio, 'getDocumentoDetalle'),
            '7' => $this->callGetter($mmercurio, 'getNomtra'),
            '8' => $this->callGetter($mmercurio, 'getNombre'),
            default => $this->resolveNombrePersona($mmercurio),
        };
    }

    private function resolveNombrePersona(object $mmercurio): string
    {
        if (method_exists($mmercurio, 'getNombre')) {
            $nombre = trim((string) $mmercurio->getNombre());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (method_exists($mmercurio, 'getNombreCompleto')) {
            $nombre = trim((string) $mmercurio->getNombreCompleto());
            if ($nombre !== '') {
                return $nombre;
            }
        }

        return trim(implode(' ', array_filter([
            $this->callGetter($mmercurio, 'getPrinom'),
            $this->callGetter($mmercurio, 'getSegnom'),
            $this->callGetter($mmercurio, 'getPriape'),
            $this->callGetter($mmercurio, 'getSegape'),
        ], fn ($part) => $part !== '')));
    }

    private function callGetter(object $mmercurio, string $getter): string
    {
        if (! method_exists($mmercurio, $getter)) {
            return '';
        }

        $value = $mmercurio->{$getter}();

        return trim((string) ($value ?? ''));
    }

    private function getResponsable(object $mmercurio): string
    {
        $gener02 = Gener02::where('usuario', $mmercurio->getUsuario())->first();

        return $gener02 ? (string) $gener02->getNombre() : '';
    }

    private function getExtra(object $mmercurio, string $tipopc): string
    {
        if ($tipopc === '5') {
            return $this->getExtraActualizacion($mmercurio);
        }

        return $this->callGetter($mmercurio, 'getNomcer');
    }

    private function getExtraActualizacion(object $mmercurio): string
    {
        $this->camposActualizacion ??= method_exists($mmercurio, 'CamposDisponibles')
            ? $mmercurio->CamposDisponibles()
            : [];

        return Mercurio33::where('actualizacion', $mmercurio->getId())
            ->get()
            ->map(fn ($row) => ($this->camposActualizacion[$row->getCampo()] ?? $row->getCampo())
                .': '.$row->getAntval().' → '.$row->getValor())
            ->implode('; ');
    }

    private function formatFecha(object $mmercurio, string $field): ?string
    {
        $getter = 'get'.ucfirst($field);
        $value = method_exists($mmercurio, $getter) ? $mmercurio->$getter() : ($mmercurio->{$field} ?? null);

        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
