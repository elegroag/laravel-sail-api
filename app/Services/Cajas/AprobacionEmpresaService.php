<?php

namespace App\Services\Cajas;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Mercurio06;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Services\Api\ApiSubsidio;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\Cajas\Solicitudes\SolicitudListService;
use App\Services\CajaServices\EmpresaServices;
use App\Services\Utils\Mercurio10Cierre;
use App\Services\Utils\NotifyEmailServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lógica del módulo de aprobación de empresas (Mercurio30, tipopc 2).
 */
class AprobacionEmpresaService extends SolicitudListService
{
    public const TIPOPC = '2';

    public const ESTADOS_SIN_ACCION = ['A', 'T'];

    public const APORTES_DEFAULT_PER_PAGE = 25;

    public const APORTES_CACHE_TTL_MINUTES = 10;

    private ?array $catalogos = null;

    public function __construct(
        private readonly EmpresaServices $empresaServices,
        private readonly NotifyEmailServices $notifyEmailServices,
    ) {}

    protected function modelClass(): string
    {
        return Mercurio30::class;
    }

    protected function tipopc(): string
    {
        return self::TIPOPC;
    }

    protected function searchColumns(): array
    {
        return ['nit', 'razsoc', 'documento', 'cedrep'];
    }

    protected function showUrl(Model $row): string
    {
        return route('cajas.aprobacionemp.show', ['id' => $row->getKey()]);
    }

    protected function extraRow(Model $row): array
    {
        return [
            'identificacion' => (string) $row->getAttribute('nit'),
            'nombre' => (string) $row->getAttribute('razsoc'),
            'recepcion' => ! empty($row->getAttribute('sat_cedrep')) ? 'SAT' : 'NORMAL',
            'edit_url' => $row->getAttribute('estado') === 'P'
                ? route('cajas.aprobacionemp.edit', ['id' => $row->getKey()])
                : null,
        ];
    }

    public function findOrFail(int $id): Mercurio30
    {
        $solicitud = Mercurio30::query()->where('id', $id)->first();
        if (! $solicitud) {
            throw new DebugException('La solicitud de empresa no se encuentra registrada.', 404);
        }

        return $solicitud;
    }

    /**
     * Acciones disponibles según estado de la solicitud y la presencia de la empresa en SISU.
     *
     * @return array<string, bool>
     */
    public function acciones(Mercurio30 $solicitud, bool $afiliadoSisu): array
    {
        $estado = (string) $solicitud->estado;
        $abierta = ! in_array($estado, self::ESTADOS_SIN_ACCION, true);

        return [
            'editar' => $estado === 'P',
            'aprobar' => $abierta,
            'devolver' => $abierta,
            'rechazar' => $abierta,
            'aportes' => $estado !== 'A',
            'cartera' => $estado !== 'A',
            'deshacer' => $estado === 'A',
            'reaprobar' => $estado === 'A' || $afiliadoSisu,
        ];
    }

    /**
     * Ficha de solo lectura en secciones. Si SISU no responde se muestran los códigos tal cual.
     *
     * @return list<array{titulo: string, campos: list<array{label: string, value: string}>}>
     */
    public function secciones(Mercurio30 $s): array
    {
        $cat = $this->catalogos();
        $tipo = Mercurio06::query()->where('tipo', $s->tipo)->first();
        $lookup = fn(string $key, mixed $code) => $this->etiqueta($cat[$key] ?? [], $code);

        return [
            [
                'titulo' => 'Información de la empresa',
                'campos' => $this->campos([
                    'Radicado (RUUID)' => $s->ruuid,
                    'NIT' => $s->nit,
                    'Dígito de verificación' => $s->digver,
                    'Razón social' => $s->razsoc,
                    'Sigla' => $s->sigla,
                    'Estado' => $s->getEstadoDetalle(),
                    'Tipo de aportante' => $tipo?->detalle,
                    'Calidad empresa' => $lookup('calemp', $s->calemp),
                    'Tipo sociedad' => $lookup('tipsoc', $s->tipsoc),
                    'Actividad' => $lookup('codact', $s->codact),
                    'Fecha inicial' => $this->fecha($s->fecini),
                    'Total trabajadores' => $s->tottra,
                    'Valor nómina' => $s->valnom,
                    'Fecha solicitud' => $this->fecha($s->fecsol),
                    'Fecha aprobación resolución' => $this->fecha($s->fecapr),
                ]),
            ],
            [
                'titulo' => 'Representante legal',
                'campos' => $this->campos([
                    'Cédula' => $s->cedrep,
                    'Nombre' => $s->repleg,
                ]),
            ],
            [
                'titulo' => 'Notificación',
                'campos' => $this->campos([
                    'Dirección' => $s->direccion,
                    'Ciudad' => $lookup('codciu', $s->codciu),
                    'Ciudad de labor de trabajadores' => $lookup('codzon', $s->codzon),
                    'Teléfono' => $s->telefono,
                    'Celular' => $s->celular,
                    'Email' => $s->email,
                ]),
            ],
            [
                'titulo' => 'Comercial',
                'campos' => $this->campos([
                    'Dirección' => $s->dirpri,
                    'Ciudad' => $lookup('ciupri', $s->ciupri),
                    'Teléfono' => $s->celpri,
                    'Email' => $s->emailpri,
                ]),
            ],
        ];
    }

    /**
     * Opciones y valores iniciales del formulario de aprobación.
     *
     * @return array{options: array<string, list<array{value: string, label: string}>>, defaults: array<string, mixed>, catalogos_disponibles: bool}
     */
    public function aprobarFormulario(Mercurio30 $solicitud): array
    {
        $cat = $this->catalogos();
        $keys = ['tipdur', 'codind', 'todmes', 'forpre', 'pymes', 'contratista', 'tipemp', 'tipapo', 'tipsoc', 'ofiafi', 'colegio'];
        $options = [];
        foreach ($keys as $key) {
            $options[$key] = $this->opciones($cat[$key] ?? []);
        }

        return [
            'options' => $options,
            'catalogos_disponibles' => ! empty($cat),
            'defaults' => [
                'tipdur' => 'S',
                'codind' => '03',
                'todmes' => 'S',
                'forpre' => 'S',
                'pymes' => 'N',
                'contratista' => 'N',
                'tipemp' => 'P',
                'tipapo' => 'P',
                'tipsoc' => (string) $solicitud->tipsoc,
                'ofiafi' => '01',
                'colegio' => 'N',
                'codsuc' => '',
                'actapr' => '',
                'diahab' => '',
                'fecafi' => '',
                'fecapr' => Carbon::now()->toDateString(),
                'nota_aprobar' => '',
                'validaciones_control' => [
                    'camara_comercio' => true,
                    'paz_y_salvo' => true,
                    'aportes_empresa' => true,
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function aprobarRules(): array
    {
        return [
            'tipdur' => 'required|string|max:5',
            'codind' => 'required|string|max:5',
            'todmes' => 'required|string|max:5',
            'forpre' => 'required|string|max:5',
            'pymes' => 'required|string|max:5',
            'contratista' => 'required|string|max:5',
            'tipemp' => 'required|string|max:5',
            'tipapo' => 'required|string|max:5',
            'tipsoc' => 'required|string|max:5',
            'ofiafi' => 'required|string|max:5',
            'colegio' => 'required|string|max:5',
            'codsuc' => 'required|string|max:10',
            'actapr' => 'required|string|max:50',
            'diahab' => 'required|integer|min:0|max:31',
            'fecafi' => 'required|date|before_or_equal:today',
            'fecapr' => 'required|date|before_or_equal:today',
            'nota_aprobar' => 'required|string|max:2000',
            'validaciones_control' => 'required|array',
            'validaciones_control.*' => 'nullable',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function aprobarAtributos(): array
    {
        return [
            'tipdur' => 'duración',
            'codind' => 'índice',
            'todmes' => 'paga mes',
            'forpre' => 'forma de presentación',
            'pymes' => 'pyme',
            'contratista' => 'contratista',
            'tipemp' => 'tipo de empresa',
            'tipapo' => 'tipo de aportante',
            'tipsoc' => 'tipo de sociedad',
            'ofiafi' => 'oficina',
            'colegio' => 'colegio',
            'codsuc' => 'sucursal planilla',
            'actapr' => 'acta de aprobación',
            'diahab' => 'día hábil de pago',
            'fecafi' => 'fecha de afiliación',
            'fecapr' => 'fecha de aprobación de resolución',
            'nota_aprobar' => 'nota',
            'validaciones_control' => 'validaciones de control',
        ];
    }

    /**
     * Aprueba la empresa y la registra en SISU.
     *
     * @param  array<string, mixed>  $data  Datos validados con aprobarRules().
     * @return bool Indica si el correo de notificación fue enviado.
     *
     * @throws DebugException
     */
    public function aprobar(int $id, array $data): bool
    {
        $solicitud = $this->findOrFail($id);
        if (in_array((string) $solicitud->estado, self::ESTADOS_SIN_ACCION, true)) {
            throw new DebugException('La solicitud no está disponible para aprobar por su estado actual.', 422);
        }

        $data['id'] = $id;
        $data['feccap'] = $data['fecafi'];
        $data['validaciones_control'] = array_map(
            fn($v) => ($v === true || $v === 'S' || $v === 1 || $v === '1' || $v === 'true') ? 'S' : 'N',
            $data['validaciones_control']
        );

        DB::beginTransaction();
        try {
            $proceso = (new ApruebaSolicitud)->main('E', $id, $data);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        try {
            $proceso->enviarMail($data['actapr'], $data['fecapr']);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Aprobación empresa: no se pudo enviar el correo', ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function devolver(int $id, string $codest, string $nota, array $camposCorregir): void
    {
        $solicitud = $this->findOrFail($id);
        $this->assertAbierta($solicitud);
        if ($solicitud->estado === 'D') {
            throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
        }

        $this->empresaServices->devolver($solicitud, $nota, $codest, $camposCorregir ? implode(';', $camposCorregir) : '');
        $this->notifyEmailServices->emailDevolver($solicitud, $this->empresaServices->msjDevolver($solicitud, nl2br(e($nota))));
    }

    public function rechazar(int $id, string $codest, string $nota): void
    {
        $solicitud = $this->findOrFail($id);
        $this->assertAbierta($solicitud);
        if ($solicitud->estado === 'X') {
            throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
        }

        $this->empresaServices->rechazar($solicitud, $nota, $codest);
        $this->notifyEmailServices->emailRechazar($solicitud, $this->empresaServices->msjRechazar($solicitud, nl2br(e($nota))));
    }

    public function reaprobar(int $id, string $nota): void
    {
        $this->findOrFail($id);
        $hoy = Carbon::now()->toDateString();

        DB::transaction(function () use ($id, $nota, $hoy) {
            Mercurio30::where('id', $id)->update(['estado' => 'A', 'fecest' => $hoy]);

            $item = (int) Mercurio10::where('tipopc', self::TIPOPC)->where('numero', $id)->max('item') + 1;
            $mercurio10 = new Mercurio10;
            $mercurio10->tipopc = self::TIPOPC;
            $mercurio10->numero = $id;
            $mercurio10->item = $item;
            $mercurio10->estado = 'A';
            $mercurio10->nota = $nota;
            $mercurio10->fecsis = $hoy;
            $mercurio10->save();

            Mercurio10Cierre::aplicarCierreRespuesta($mercurio10);
        });
    }

    /**
     * Deshace la afiliación en SISU y actualiza el estado de la solicitud.
     *
     * @return array{success: bool, msj: string}
     *
     * @throws DebugException
     */
    public function deshacer(int $id, string $action, ?string $codest, string $nota, bool $sendEmail): array
    {
        $solicitud = Mercurio30::query()->where('id', $id)->where('estado', 'A')->first();
        if (! $solicitud) {
            throw new DebugException('Los datos de la empresa no son válidos para procesar.', 501);
        }

        $api = new ApiSubsidio;
        $api->send([
            'servicio' => 'ComfacaAfilia',
            'metodo' => 'deshacer_aprobacion_empresa',
            'params' => [
                'nit' => $solicitud->getNit(),
                'documento' => $solicitud->getDocumento(),
                'tipo_documento' => $solicitud->getTipdoc(),
                'fecha_aprueba' => $solicitud->getFecest(),
                'nota' => $nota,
            ],
        ]);

        if ($api->isJson() === false) {
            throw new DebugException('Error al procesar el deshacer la aprobación en SisuWeb.', 501);
        }

        $respuesta = $api->toArray();
        if (($respuesta['success'] ?? false) !== true) {
            throw new DebugException((string) ($respuesta['message'] ?? 'SisuWeb rechazó la operación.'), 501);
        }

        $datos = $respuesta['data'] ?? [];
        if (! empty($datos['noAction'])) {
            return [
                'success' => false,
                'msj' => 'No se realizó ninguna acción, el estado de la empresa no es válido para realizar la acción requerida.',
            ];
        }

        $eliminada = ! empty($datos['isDelete']) || ! empty($datos['isDeleteTrayecto']);
        if (! $eliminada) {
            return [
                'success' => false,
                'msj' => 'No se realizó el cambio requerido, se debe comunicar al área de soporte de las TICS.',
            ];
        }

        match ($action) {
            'D' => $this->deshacerComoDevuelta($solicitud, $nota, (string) $codest, $sendEmail),
            'R' => $this->deshacerComoRechazada($solicitud, $nota, (string) $codest, $sendEmail),
            default => $this->deshacerComoInactiva($solicitud),
        };

        return ['success' => true, 'msj' => 'Se completó el proceso con éxito.'];
    }

    /**
     * Estado de la empresa en SISU. Nunca lanza: si el servicio falla retorna consultado=false.
     *
     * @return array{consultado: bool, existe: bool, afiliada: bool, estado: ?string, datos: list<array{label: string, value: string}>}
     */
    public function consultarSisu(string $nit): array
    {
        $vacio = ['consultado' => false, 'existe' => false, 'afiliada' => false, 'estado' => null, 'datos' => []];

        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $nit],
            ]);
            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación empresa: SISU no disponible', ['nit' => $nit, 'error' => $e->getMessage()]);

            return $vacio;
        }

        $empresa = $out['data'] ?? null;
        if (! ($out['success'] ?? false) || ! is_array($empresa)) {
            return ['consultado' => true] + $vacio;
        }

        $estado = isset($empresa['estado']) ? (string) $empresa['estado'] : null;
        $datos = [];
        foreach ($empresa as $key => $value) {
            if (is_scalar($value) && $value !== '' && count($datos) < 24) {
                $datos[] = ['label' => strtoupper((string) $key), 'value' => (string) $value];
            }
        }

        return [
            'consultado' => true,
            'existe' => true,
            'afiliada' => in_array($estado, ['A', 'S', 'D'], true),
            'estado' => $estado,
            'datos' => $datos,
        ];
    }

    /**
     * Consulta aportes en SISU, cachea el dataset completo y pagina en memoria.
     *
     * @param  array{page?: int, per_page?: int, refresh?: bool}  $filtros
     * @return array{success: bool, msj: string, from_cache: bool, aportes: list<array<string, mixed>>, pagination: array<string, int|null>, per_page_options: list<int>}
     */
    public function aportes(Mercurio30 $solicitud, array $filtros = []): array
    {
        $page = max(1, (int) ($filtros['page'] ?? 1));
        $perPage = (int) ($filtros['per_page'] ?? self::APORTES_DEFAULT_PER_PAGE);
        if (! in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = self::APORTES_DEFAULT_PER_PAGE;
        }
        $refresh = (bool) ($filtros['refresh'] ?? false);

        $nit = (string) $solicitud->getNit();
        $cacheKey = $this->aportesCacheKey($nit);
        $fromCache = false;

        if ($refresh || ! Cache::has($cacheKey)) {
            $payload = $this->fetchAportesFromSisu($solicitud);
            if ($payload['success']) {
                Cache::put($cacheKey, $payload, now()->addMinutes(self::APORTES_CACHE_TTL_MINUTES));
            } else {
                Cache::forget($cacheKey);
            }
        } else {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && isset($cached['aportes']) && is_array($cached['aportes'])) {
                $payload = [
                    'success' => (bool) ($cached['success'] ?? true),
                    'msj' => (string) ($cached['msj'] ?? ''),
                    'aportes' => $cached['aportes'],
                ];
                $fromCache = true;
            } else {
                $payload = $this->fetchAportesFromSisu($solicitud);
                if ($payload['success']) {
                    Cache::put($cacheKey, $payload, now()->addMinutes(self::APORTES_CACHE_TTL_MINUTES));
                }
            }
        }

        /** @var list<array<string, mixed>> $allRows */
        $allRows = $payload['aportes'];
        $total = count($allRows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;
        $pageRows = array_values(array_slice($allRows, $offset, $perPage));
        $from = $total === 0 ? null : $offset + 1;
        $to = $total === 0 ? null : min($offset + $perPage, $total);

        return [
            'success' => (bool) $payload['success'],
            'msj' => (string) $payload['msj'],
            'from_cache' => $fromCache,
            'aportes' => $pageRows,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'from' => $from,
                'to' => $to,
                'total' => $total,
            ],
            'per_page_options' => self::PER_PAGE_OPTIONS,
        ];
    }

    public function aportesCacheKey(string $nit): string
    {
        return 'cajas:aprobacionemp:aportes:'.trim($nit);
    }

    /**
     * @return array{success: bool, msj: string, aportes: list<array<string, mixed>>}
     */
    private function fetchAportesFromSisu(Mercurio30 $solicitud): array
    {
        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'AportesEmpresas',
                'metodo' => 'buscarAportesEmpresa',
                'params' => ['nit' => $solicitud->getNit()],
            ]);

            if ($api->isJson() === false) {
                return ['success' => false, 'msj' => 'Error procesando la consulta de aportes.', 'aportes' => []];
            }

            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación empresa: error consultando aportes', ['id' => $solicitud->id, 'error' => $e->getMessage()]);

            return ['success' => false, 'msj' => 'No fue posible consultar los aportes en SISU.', 'aportes' => []];
        }

        $filas = collect($out['data'] ?? [])->map(fn ($row) => [
            'perapo' => $row['perapo'] ?? '',
            'fecrec' => $row['fecrec'] ?? '',
            'fecsis' => $row['fecsis'] ?? '',
            'cedtra' => $row['cedtra'] ?? '',
            'codsuc' => $row['codsuc'] ?? '',
            'numero' => $row['numero'] ?? '',
            'valapo' => $row['valapo'] ?? '',
            'valnom' => $row['valnom'] ?? '',
        ])->values()->all();

        return [
            'success' => (bool) ($out['success'] ?? false),
            'msj' => (string) ($out['message'] ?? ''),
            'aportes' => $filas,
        ];
    }

    /**
     * Consulta cartera / mora presunta en SISU, cachea el dataset y pagina en memoria.
     *
     * @param  array{page?: int, per_page?: int, refresh?: bool}  $filtros
     * @return array{success: bool, msj: string, from_cache: bool, rows: list<array<string, mixed>>, pagination: array<string, int|null>, per_page_options: list<int>}
     */
    public function cartera(Mercurio30 $solicitud, array $filtros = []): array
    {
        $page = max(1, (int) ($filtros['page'] ?? 1));
        $perPage = (int) ($filtros['per_page'] ?? self::APORTES_DEFAULT_PER_PAGE);
        if (! in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = self::APORTES_DEFAULT_PER_PAGE;
        }
        $refresh = (bool) ($filtros['refresh'] ?? false);

        $nit = (string) $solicitud->getNit();
        $cacheKey = $this->carteraCacheKey($nit);
        $fromCache = false;

        if ($refresh || ! Cache::has($cacheKey)) {
            $payload = $this->fetchCarteraFromSisu($solicitud);
            if ($payload['success']) {
                Cache::put($cacheKey, $payload, now()->addMinutes(self::APORTES_CACHE_TTL_MINUTES));
            } else {
                Cache::forget($cacheKey);
            }
        } else {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && isset($cached['rows']) && is_array($cached['rows'])) {
                $payload = [
                    'success' => (bool) ($cached['success'] ?? true),
                    'msj' => (string) ($cached['msj'] ?? ''),
                    'rows' => $cached['rows'],
                ];
                $fromCache = true;
            } else {
                $payload = $this->fetchCarteraFromSisu($solicitud);
                if ($payload['success']) {
                    Cache::put($cacheKey, $payload, now()->addMinutes(self::APORTES_CACHE_TTL_MINUTES));
                }
            }
        }

        /** @var list<array<string, mixed>> $allRows */
        $allRows = $payload['rows'];
        $total = count($allRows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;
        $pageRows = array_values(array_slice($allRows, $offset, $perPage));
        $from = $total === 0 ? null : $offset + 1;
        $to = $total === 0 ? null : min($offset + $perPage, $total);

        return [
            'success' => (bool) $payload['success'],
            'msj' => (string) $payload['msj'],
            'from_cache' => $fromCache,
            'rows' => $pageRows,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'from' => $from,
                'to' => $to,
                'total' => $total,
            ],
            'per_page_options' => self::PER_PAGE_OPTIONS,
        ];
    }

    public function carteraCacheKey(string $nit): string
    {
        return 'cajas:aprobacionemp:cartera:'.trim($nit);
    }

    /**
     * @return array{success: bool, msj: string, rows: list<array<string, mixed>>}
     */
    private function fetchCarteraFromSisu(Mercurio30 $solicitud): array
    {
        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'AportesEmpresas',
                'metodo' => 'cartera_mora_presunta_by_nit',
                'params' => ['nit' => $solicitud->getNit()],
            ]);

            if ($api->isJson() === false) {
                return ['success' => false, 'msj' => 'Error procesando la consulta de cartera.', 'rows' => []];
            }

            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación empresa: error consultando cartera', ['id' => $solicitud->id, 'error' => $e->getMessage()]);

            return ['success' => false, 'msj' => 'No fue posible consultar la cartera en SISU.', 'rows' => []];
        }

        $cartera = $out['data']['cartera'] ?? [];
        if (! is_array($cartera)) {
            $cartera = [];
        }

        $filas = [];
        foreach ($cartera as $codsuc => $periodos) {
            if (! is_array($periodos)) {
                continue;
            }
            foreach ($periodos as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $filas[] = [
                    'codsuc' => (string) ($row['codsuc'] ?? $codsuc),
                    'periodo' => (string) ($row['periodo'] ?? ''),
                    'valcar' => $row['valcar'] ?? 0,
                ];
            }
        }

        usort($filas, function (array $a, array $b): int {
            $bySuc = strcmp((string) $a['codsuc'], (string) $b['codsuc']);
            if ($bySuc !== 0) {
                return $bySuc;
            }

            return strcmp((string) $a['periodo'], (string) $b['periodo']);
        });

        return [
            'success' => (bool) ($out['success'] ?? false),
            'msj' => (string) ($out['message'] ?? $out['msj'] ?? ''),
            'rows' => array_values($filas),
        ];
    }

    /**
     * Catálogos de SISU. Se cargan una sola vez; si el servicio falla quedan vacíos.
     *
     * @return array<string, array<string|int, string>>
     */
    public function catalogos(): array
    {
        if ($this->catalogos !== null) {
            return $this->catalogos;
        }

        try {
            $api = new ApiSubsidio;
            $api->send(['servicio' => 'ComfacaAfilia', 'metodo' => 'parametros_empresa']);
            (new ParamsEmpresa)->setDatosCaptura($api->toArray());

            $getters = [
                'tipdur' => 'getTipoDuracion',
                'codind' => 'getCodigoIndice',
                'todmes' => 'getPagaMes',
                'forpre' => 'getFormaPresentacion',
                'tipsoc' => 'getTipoSociedades',
                'pymes' => 'getPymes',
                'contratista' => 'getContratista',
                'tipemp' => 'getTipoEmpresa',
                'tipapo' => 'getTipoAportante',
                'ofiafi' => 'getOficina',
                'colegio' => 'getColegio',
                'calemp' => 'getCalidadEmpresa',
                'codciu' => 'getCiudades',
                'codzon' => 'getZonas',
                'codact' => 'getActividades',
                'ciupri' => 'getCiudadesComerciales',
            ];

            $catalogos = [];
            foreach ($getters as $key => $method) {
                try {
                    $catalogos[$key] = (array) ParamsEmpresa::$method();
                } catch (\Throwable) {
                    $catalogos[$key] = [];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Aprobación empresa: parámetros SISU no disponibles', ['error' => $e->getMessage()]);
            $catalogos = [];
        }

        return $this->catalogos = $catalogos;
    }

    private function assertAbierta(Mercurio30 $solicitud): void
    {
        if (in_array((string) $solicitud->estado, self::ESTADOS_SIN_ACCION, true)) {
            throw new DebugException('La solicitud no admite esta acción por su estado actual.', 422);
        }
    }

    private function deshacerComoDevuelta(Mercurio30 $solicitud, string $nota, string $codest, bool $sendEmail): void
    {
        $this->empresaServices->devolver($solicitud, $nota, $codest, '');
        if ($sendEmail) {
            $this->notifyEmailServices->emailDevolver($solicitud, $this->empresaServices->msjDevolver($solicitud, nl2br(e($nota))));
        }
    }

    private function deshacerComoRechazada(Mercurio30 $solicitud, string $nota, string $codest, bool $sendEmail): void
    {
        $this->empresaServices->rechazar($solicitud, $nota, $codest);
        if ($sendEmail) {
            $this->notifyEmailServices->emailRechazar($solicitud, $this->empresaServices->msjRechazar($solicitud, nl2br(e($nota))));
        }
    }

    private function deshacerComoInactiva(Mercurio30 $solicitud): void
    {
        $solicitud->setEstado('I');
        $solicitud->setFecest(date('Y-m-d'));
        $solicitud->save();
    }

    /**
     * @param  array<string, mixed>  $pares
     * @return list<array{label: string, value: string}>
     */
    private function campos(array $pares): array
    {
        $campos = [];
        foreach ($pares as $label => $value) {
            $campos[] = ['label' => $label, 'value' => $value === null ? '' : (string) $value];
        }

        return $campos;
    }

    /**
     * @param  array<string|int, string>  $catalogo
     */
    private function etiqueta(array $catalogo, mixed $code): string
    {
        if ($code === null || $code === '') {
            return '';
        }

        return (string) ($catalogo[$code] ?? $code);
    }

    /**
     * @param  array<string|int, string>  $catalogo
     * @return list<array{value: string, label: string}>
     */
    private function opciones(array $catalogo): array
    {
        $opciones = [];
        foreach ($catalogo as $value => $label) {
            $opciones[] = ['value' => (string) $value, 'label' => (string) $label];
        }

        return $opciones;
    }
}
