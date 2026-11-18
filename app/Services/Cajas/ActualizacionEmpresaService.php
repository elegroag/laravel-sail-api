<?php

namespace App\Services\Cajas;

use App\Library\Collections\ParamsEmpresa;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio33;
use App\Models\Mercurio47;
use App\Services\Aprueba\ValidacionControlChecklist;
use App\Services\Cajas\Solicitudes\SolicitudAprobacionService;
use App\Services\CajaServices\DevolucionNotificacion;
use App\Services\CajaServices\UpDatosEmpresaServices;
use App\Services\Utils\Mercurio10Cierre;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Aprobación de actualizaciones de datos de empresa (Mercurio47 tipact E + cambios en Mercurio33, tipopc 5).
 */
class ActualizacionEmpresaService extends SolicitudAprobacionService
{
    public const TIPOPC = '5';

    /** @var array<string, string> */
    private array $nombres = [];

    public function routePrefix(): string
    {
        return 'cajas.actualizaemp';
    }

    protected function modelClass(): string
    {
        return Mercurio47::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function calemp(): string
    {
        return 'UE';
    }

    protected function legacy(): object
    {
        return new UpDatosEmpresaServices;
    }

    protected function paramsClass(): string
    {
        return ParamsEmpresa::class;
    }

    protected function paramsMetodo(): string
    {
        return 'parametros_empresa';
    }

    protected function searchColumns(): array
    {
        return ['documento'];
    }

    protected function scope(Builder $query): Builder
    {
        return $query->where('tipact', $this->tipact());
    }

    protected function controlesVariant(): string
    {
        return ValidacionControlChecklist::VARIANT_EMPRESA;
    }

    /** Tipo de actualización dentro de Mercurio47 (también es el tipo de campo de Mercurio28). */
    protected function tipact(): string
    {
        return 'E';
    }

    protected function notificaPorCorreo(): bool
    {
        return false;
    }

    public function capacidades(): array
    {
        return ['editar' => false, 'aportes' => false, 'deshacer' => false, 'reaprobar' => false];
    }

    protected function identificacion(Model $row): string
    {
        return (string) $row->getAttribute('documento');
    }

    protected function sisuDocumento(Model $solicitud): string
    {
        return (string) $solicitud->getAttribute('documento');
    }

    protected function nombre(Model $row): string
    {
        $key = implode('|', [$row->getAttribute('tipo'), $row->getAttribute('coddoc'), $row->getAttribute('documento')]);

        return $this->nombres[$key] ??= (string) Mercurio07::query()
            ->where('tipo', $row->getAttribute('tipo'))
            ->where('coddoc', $row->getAttribute('coddoc'))
            ->where('documento', $row->getAttribute('documento'))
            ->value('nombre');
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [
            'codind' => '03',
            'todmes' => 'S',
            'forpre' => 'S',
            'tipemp' => 'P',
            'ofiafi' => '01',
            'colegio' => 'N',
            'contratista' => 'N',
            'tipdur' => 'S',
            'pymes' => 'N',
            'tipapo' => 'P',
            'actapr' => '',
            'nota' => $data['nota_aprobar'],
        ];
    }

    protected function aprobarCampos(): array
    {
        return [
            ['name' => 'codsuc', 'label' => 'Sucursal planilla', 'required' => false, 'max' => 10],
            ['name' => 'fecapr', 'label' => 'Fecha aprobación', 'type' => 'date'],
        ];
    }

    public function secciones(Model $solicitud): array
    {
        $cat = $this->catalogos();

        return [
            [
                'titulo' => 'Solicitud de actualización de datos',
                'campos' => $this->campos([
                    'Radicado (RUUID)' => $solicitud->getAttribute('ruuid'),
                    'NIT' => $solicitud->getAttribute('documento'),
                    'Tipo de documento' => $this->etiqueta($cat['coddoc'] ?? [], $solicitud->getAttribute('coddoc')),
                    'Solicitante' => $this->nombre($solicitud),
                    'Estado' => $this->estadoDetalle($solicitud),
                    'Fecha de solicitud' => $this->fecha($solicitud->getAttribute('fecsol')),
                    'Fecha de aprobación' => $this->fecha($solicitud->getAttribute('fecapr')),
                ]),
            ],
        ];
    }

    /**
     * Campos que el solicitante pidió modificar, con el valor anterior y el nuevo.
     *
     * @return list<array{campo: string, label: string, anterior: string, nuevo: string}>
     */
    public function cambios(Model $solicitud): array
    {
        $etiquetas = DB::table('mercurio28')->where('tipo', $this->tipact())->pluck('detalle', 'campo');

        return Mercurio33::query()
            ->where('actualizacion', $solicitud->getKey())
            ->orderBy('id')
            ->get()
            ->map(fn (Mercurio33 $row) => [
                'campo' => (string) $row->campo,
                'label' => (string) ($etiquetas[$row->campo] ?? $row->campo),
                'anterior' => (string) ($row->antval ?? ''),
                'nuevo' => (string) ($row->valor ?? ''),
            ])
            ->values()
            ->all();
    }

    protected function aplicarDevolucion(Model $solicitud, string $nota, string $codest, string $camposCorregir): void
    {
        $this->registrarRespuesta($solicitud, 'D', $nota, $codest, $camposCorregir);
        DevolucionNotificacion::notificar($solicitud, $nota, 'Solicitud de actualización de datos empresa devolución');
    }

    protected function aplicarRechazo(Model $solicitud, string $nota, string $codest): void
    {
        $this->registrarRespuesta($solicitud, 'X', $nota, $codest, null);
    }

    protected function registrarRespuesta(Model $solicitud, string $estado, string $nota, string $codest, ?string $camposCorregir): void
    {
        $hoy = Carbon::now()->toDateString();
        $id = $solicitud->getKey();
        $tipopc = $this->tipopc();

        DB::transaction(function () use ($solicitud, $estado, $nota, $codest, $camposCorregir, $hoy, $id, $tipopc) {
            $solicitud->setAttribute('estado', $estado);
            $solicitud->setAttribute('codest', $codest);
            $solicitud->setAttribute('fecest', $hoy);
            $solicitud->save();

            $mercurio10 = Mercurio10::create([
                'tipopc' => $tipopc,
                'numero' => $id,
                'item' => (int) Mercurio10::where('tipopc', $tipopc)->where('numero', $id)->max('item') + 1,
                'estado' => $estado,
                'nota' => $nota,
                'codest' => $codest,
                'fecsis' => $hoy,
                'campos_corregir' => $camposCorregir,
            ]);

            Mercurio10Cierre::aplicarCierreRespuesta($mercurio10);
        });
    }
}
