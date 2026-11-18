<?php

namespace App\Services\Cajas\Solicitudes;

use App\Exceptions\DebugException;
use App\Models\Mercurio06;
use App\Models\Mercurio10;
use App\Services\Api\ApiSubsidio;
use App\Services\Aprueba\ApruebaSolicitud;
use App\Services\Aprueba\ValidacionControlChecklist;
use App\Services\Utils\Mercurio10Cierre;
use App\Services\Utils\NotifyEmailServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lógica común de aprobación de solicitudes de afiliación de personas
 * (independientes, pensionados, facultativos) y actualización de datos de empresa.
 */
abstract class SolicitudAprobacionService extends SolicitudListService
{
    public const ESTADOS_SIN_ACCION = ['A', 'T'];

    public const NOTA_MAX = 2000;

    public const PAGOS = [
        'T' => 'PENDIENTE DEFINIR FORMA DE PAGO',
        'A' => 'ABONO A CUENTA DE BANCO',
        'D' => 'DAVIPLATA',
    ];

    private const CONTROLES = [
        'guias' => 'Validó información en GUIAS',
        'adres' => 'Validó información en ADRES',
        'ruaf' => 'Validó información en RUAF',
        'cobertura_salud' => 'Validó cobertura en salud / condición de pensionado',
        'no_afiliado_otro_trabajador' => 'Validó que no está afiliado a esta Caja por otro trabajador',
        'camara_comercio' => 'Validación de Cámara de Comercio',
        'paz_y_salvo' => 'Se valida paz y salvo',
        'aportes_empresa' => 'Validación de aportes de la empresa',
    ];

    private ?array $catalogos = null;

    public function __construct(protected readonly NotifyEmailServices $notifyEmailServices) {}

    /** Prefijo de rutas nombradas, p. ej. `cajas.aprobaindepen`. */
    abstract public function routePrefix(): string;

    abstract protected function calemp(): string;

    /** Servicio legacy con devolver/rechazar/msjDevolver/msjRechazar. */
    abstract protected function legacy(): object;

    /** @return class-string */
    abstract protected function paramsClass(): string;

    abstract protected function paramsMetodo(): string;

    abstract protected function identificacion(Model $row): string;

    abstract protected function nombre(Model $row): string;

    /**
     * @return list<array{titulo: string, campos: list<array{label: string, value: string}>}>
     */
    abstract public function secciones(Model $solicitud): array;

    /**
     * Definición de los campos del formulario de aprobación.
     *
     * @return list<array<string, mixed>>
     */
    abstract protected function aprobarCampos(): array;

    /**
     * @return array{editar: bool, aportes: bool, deshacer: bool, reaprobar: bool}
     */
    abstract public function capacidades(): array;

    /**
     * Valores propios del flujo que no se piden al asesor y se envían al proceso de aprobación.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [];
    }

    protected function deshacerMetodo(): ?string
    {
        return null;
    }

    /** Si la devolución y el rechazo notifican por correo (el legacy lo hacía desde el controlador). */
    protected function notificaPorCorreo(): bool
    {
        return true;
    }

    protected function controlesVariant(): string
    {
        return ValidacionControlChecklist::VARIANT_DEFAULT;
    }

    protected function notaMin(): int
    {
        return 1;
    }

    /**
     * @return list<string>
     */
    protected function indicesValidos(): array
    {
        return [];
    }

    protected function sisuDocumento(Model $solicitud): string
    {
        return (string) $solicitud->getAttribute('cedtra');
    }

    public function etiquetaSolicitud(): string
    {
        return 'solicitud';
    }

    public function estadoDetalle(Model $solicitud): string
    {
        return method_exists($solicitud, 'getEstadoDetalle')
            ? (string) $solicitud->getEstadoDetalle()
            : (string) (estado_detalle_value((string) $solicitud->getAttribute('estado')) ?? $solicitud->getAttribute('estado'));
    }

    public function routeName(string $name): string
    {
        return $this->routePrefix().'.'.$name;
    }

    protected function showUrl(Model $row): string
    {
        return route($this->routeName('show'), ['id' => $row->getKey()]);
    }

    protected function extraRow(Model $row): array
    {
        return [
            'identificacion' => $this->identificacion($row),
            'nombre' => $this->nombre($row),
            'recepcion' => '',
            'edit_url' => $this->capacidades()['editar'] && $row->getAttribute('estado') === 'P'
                ? route($this->routeName('edit'), ['id' => $row->getKey()])
                : null,
        ];
    }

    public function findOrFail(int $id): Model
    {
        $solicitud = $this->scope($this->modelClass()::query())->where('id', $id)->first();
        if (! $solicitud) {
            throw new DebugException('La solicitud no se encuentra registrada.', 404);
        }

        return $solicitud;
    }

    /**
     * @return array<string, mixed>
     */
    public function resumen(Model $solicitud): array
    {
        return [
            'id' => (int) $solicitud->getKey(),
            'nit' => $this->identificacion($solicitud),
            'razsoc' => $this->nombre($solicitud),
            'estado' => (string) $solicitud->getAttribute('estado'),
            'estado_detalle' => $this->estadoDetalle($solicitud),
            'motivo' => (string) ($solicitud->getAttribute('motivo') ?? ''),
            'urls' => [
                'index' => route($this->routeName('index')),
                'show' => route($this->routeName('show'), ['id' => $solicitud->getKey()]),
            ],
        ];
    }

    /**
     * Acciones disponibles según el estado, la presencia en SISU y las capacidades del módulo.
     *
     * @return array<string, bool>
     */
    public function acciones(Model $solicitud, bool $afiliadoSisu): array
    {
        $estado = (string) $solicitud->getAttribute('estado');
        $abierta = ! in_array($estado, self::ESTADOS_SIN_ACCION, true);
        $cap = $this->capacidades();

        return [
            'editar' => $cap['editar'] && $estado === 'P',
            'aprobar' => $abierta,
            'devolver' => $abierta,
            'rechazar' => $abierta,
            'aportes' => $cap['aportes'] && $estado !== 'A',
            'deshacer' => $cap['deshacer'] && $estado === 'A',
            'reaprobar' => $cap['reaprobar'] && ($estado === 'A' || $afiliadoSisu),
        ];
    }

    /**
     * Opciones y valores iniciales del formulario de aprobación.
     *
     * @return array{campos: list<array<string, mixed>>, defaults: array<string, mixed>, controles: list<array{key: string, label: string}>, catalogos_disponibles: bool, dependencias: array<string, array<string, array<string, string>>>, nota_max: int}
     */
    public function aprobarFormulario(Model $solicitud): array
    {
        $cat = $this->catalogos();
        $validos = $this->indicesValidos();
        $campos = [];
        $defaults = [];

        foreach ($this->aprobarCampos() as $campo) {
            $campo = array_merge(['type' => 'text', 'required' => true, 'search' => false, 'max' => null], $campo);
            if ($campo['type'] === 'select') {
                $opciones = $campo['opciones'] ?? $this->opciones($cat[$campo['catalogo'] ?? $campo['name']] ?? []);
                if ($campo['name'] === 'codind' && $validos) {
                    $opciones = $this->filtrarIndices($opciones, $validos);
                }
                $campo['options'] = $opciones;
            }
            $defaults[$campo['name']] = $campo['default'] ?? '';
            unset($campo['opciones'], $campo['catalogo'], $campo['default']);
            $campos[] = $campo;
        }

        return [
            'campos' => $campos,
            'defaults' => array_merge($defaults, [
                'fecapr' => Carbon::now()->toDateString(),
                'nota_aprobar' => '',
                'validaciones_control' => array_fill_keys(ValidacionControlChecklist::keysFor($this->controlesVariant()), true),
            ]),
            'controles' => array_map(
                fn (string $key) => ['key' => $key, 'label' => self::CONTROLES[$key]],
                ValidacionControlChecklist::keysFor($this->controlesVariant())
            ),
            'catalogos_disponibles' => ! empty($cat),
            'dependencias' => $this->dependenciasAprobar(),
            'nota_max' => self::NOTA_MAX,
        ];
    }

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    protected function dependenciasAprobar(): array
    {
        $tienePago = in_array('tippag', array_column($this->aprobarCampos(), 'name'), true);

        // Daviplata fija el banco y el tipo de cuenta, como en el flujo legacy.
        return $tienePago ? ['tippag' => ['D' => ['codban' => '51', 'tipcue' => 'A']]] : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function aprobarRules(): array
    {
        $rules = [];
        foreach ($this->aprobarCampos() as $campo) {
            $campo = array_merge(['type' => 'text', 'required' => true, 'max' => null], $campo);
            $reglas = [];

            if (isset($campo['required_if'])) {
                $reglas[] = 'required_if:'.$campo['required_if']['field'].','.implode(',', $campo['required_if']['in']);
                $reglas[] = 'nullable';
            } else {
                $reglas[] = $campo['required'] ? 'required' : 'nullable';
            }

            $reglas = array_merge($reglas, match ($campo['type']) {
                'number' => ['integer', 'min:'.($campo['min'] ?? 0), 'max:'.($campo['max'] ?? 31)],
                'date' => ['date', 'before_or_equal:today'],
                'select' => ['string', 'max:'.($campo['max'] ?? 10)],
                default => ['string', 'max:'.($campo['max'] ?? 50)],
            });

            $rules[$campo['name']] = $reglas;
        }

        $rules['nota_aprobar'] = ['required', 'string', 'min:'.$this->notaMin(), 'max:'.self::NOTA_MAX];
        $rules['validaciones_control'] = ['required', 'array'];
        $rules['validaciones_control.*'] = ['nullable'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function aprobarAtributos(): array
    {
        $atributos = ['nota_aprobar' => 'nota', 'validaciones_control' => 'validaciones de control'];
        foreach ($this->aprobarCampos() as $campo) {
            $atributos[$campo['name']] = mb_strtolower((string) $campo['label']);
        }

        return $atributos;
    }

    /**
     * Aprueba la solicitud y la registra en SISU.
     *
     * @param  array<string, mixed>  $data  Datos validados con aprobarRules().
     * @return bool Indica si el correo de notificación fue enviado.
     *
     * @throws DebugException
     */
    public function aprobar(int $id, array $data): bool
    {
        $solicitud = $this->findOrFail($id);
        $this->assertAbierta($solicitud);

        $data['id'] = $id;
        $data['feccap'] = $data['fecafi'] ?? $data['fecapr'];
        $data['validaciones_control'] = array_map(
            fn ($v) => ($v === true || $v === 'S' || $v === 1 || $v === '1' || $v === 'true') ? 'S' : 'N',
            $data['validaciones_control']
        );
        $data = array_merge($data, $this->aprobarFijos($solicitud, $data));

        DB::beginTransaction();
        try {
            $proceso = (new ApruebaSolicitud)->main($this->calemp(), $id, $data);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        try {
            $proceso->enviarMail((string) ($data['actapr'] ?? ''), (string) $data['fecapr']);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Aprobación de solicitud: no se pudo enviar el correo', ['id' => $id, 'tipopc' => $this->tipopc(), 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function devolver(int $id, string $codest, string $nota, array $camposCorregir): void
    {
        $solicitud = $this->findOrFail($id);
        $this->assertAbierta($solicitud);
        if ($solicitud->getAttribute('estado') === 'D') {
            throw new DebugException('El registro ya se encuentra devuelto, no se requiere de repetir la acción.', 201);
        }

        $this->aplicarDevolucion($solicitud, $nota, $codest, $camposCorregir ? implode(';', $camposCorregir) : '');
        if ($this->notificaPorCorreo()) {
            $this->notifyEmailServices->emailDevolver($solicitud, $this->legacy()->msjDevolver($solicitud, nl2br(e($nota))));
        }
    }

    public function rechazar(int $id, string $codest, string $nota): void
    {
        $solicitud = $this->findOrFail($id);
        $this->assertAbierta($solicitud);
        if ($solicitud->getAttribute('estado') === 'X') {
            throw new DebugException('El registro ya se encuentra rechazado, no se requiere de repetir la acción.', 201);
        }

        $this->aplicarRechazo($solicitud, $nota, $codest);
        if ($this->notificaPorCorreo()) {
            $this->notifyEmailServices->emailRechazar($solicitud, $this->legacy()->msjRechazar($solicitud, nl2br(e($nota))));
        }
    }

    public function reaprobar(int $id, string $nota): void
    {
        $this->findOrFail($id);
        $hoy = Carbon::now()->toDateString();
        $tipopc = $this->tipopc();

        DB::transaction(function () use ($id, $nota, $hoy, $tipopc) {
            $this->modelClass()::query()->where('id', $id)->update(['estado' => 'A', 'fecest' => $hoy]);

            $mercurio10 = new Mercurio10;
            $mercurio10->tipopc = $tipopc;
            $mercurio10->numero = $id;
            $mercurio10->item = (int) Mercurio10::where('tipopc', $tipopc)->where('numero', $id)->max('item') + 1;
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
        $metodo = $this->deshacerMetodo();
        $solicitud = $this->findOrFail($id);
        if ($metodo === null || $solicitud->getAttribute('estado') !== 'A') {
            throw new DebugException('Los datos de la solicitud no son válidos para procesar.', 501);
        }

        $api = new ApiSubsidio;
        $api->send([
            'servicio' => 'ComfacaAfilia',
            'metodo' => $metodo,
            'params' => [
                'cedtra' => $solicitud->getAttribute('cedtra'),
                'coddoc' => $solicitud->getAttribute('tipdoc'),
                'fecafi' => $solicitud->getAttribute('fecapr'),
                'fecapr' => $solicitud->getAttribute('fecapr'),
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
                'msj' => 'No se realizó ninguna acción, el estado de la solicitud no es válido para realizar la acción requerida.',
            ];
        }

        if (empty($datos['isDelete']) && empty($datos['isDeleteTrayecto'])) {
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
    public function consultarSisu(Model $solicitud): array
    {
        $vacio = ['consultado' => false, 'existe' => false, 'afiliada' => false, 'estado' => null, 'datos' => []];
        $documento = $this->sisuDocumento($solicitud);

        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $documento],
            ]);
            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación de solicitud: SISU no disponible', ['documento' => $documento, 'error' => $e->getMessage()]);

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
     * @return array{success: bool, msj: string, aportes: list<array<string, mixed>>}
     */
    public function aportes(Model $solicitud): array
    {
        try {
            $api = new ApiSubsidio;
            $api->send([
                'servicio' => 'AportesEmpresas',
                'metodo' => 'buscarAportesEmpresa',
                'params' => $this->sisuDocumento($solicitud),
            ]);

            if ($api->isJson() === false) {
                return ['success' => false, 'msj' => 'Error procesando la consulta de aportes.', 'aportes' => []];
            }

            $out = $api->toArray();
        } catch (\Throwable $e) {
            Log::warning('Aprobación de solicitud: error consultando aportes', ['id' => $solicitud->getKey(), 'error' => $e->getMessage()]);

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
     * Catálogos de SISU. Se cargan una sola vez; si el servicio falla quedan vacíos.
     *
     * @return array<string, array<string|int, string>>
     */
    public function catalogos(): array
    {
        if ($this->catalogos !== null) {
            return $this->catalogos;
        }

        $class = $this->paramsClass();

        try {
            $api = new ApiSubsidio;
            $api->send(['servicio' => 'ComfacaAfilia', 'metodo' => $this->paramsMetodo()]);
            (new $class)->setDatosCaptura($api->toArray());

            $catalogos = [];
            foreach ($this->catalogoGetters() as $key => $method) {
                try {
                    $catalogos[$key] = method_exists($class, $method) ? (array) $class::$method() : [];
                } catch (\Throwable) {
                    $catalogos[$key] = [];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Aprobación de solicitud: parámetros SISU no disponibles', ['error' => $e->getMessage()]);
            $catalogos = [];
        }

        return $this->catalogos = $catalogos;
    }

    /**
     * @return array<string, string>
     */
    protected function catalogoGetters(): array
    {
        return [
            'codind' => 'getCodigoIndice',
            'todmes' => 'getPagaMes',
            'tipdur' => 'getTipoDuracion',
            'forpre' => 'getFormaPresentacion',
            'tipsoc' => 'getTipoSociedades',
            'tipapo' => 'getTipoAportante',
            'codban' => 'getBancos',
            'tipcue' => 'getTipoCuenta',
            'giro' => 'getGiro',
            'codgir' => 'getCodigoGiro',
            'calemp' => 'getCalidadEmpresa',
            'codciu' => 'getCiudades',
            'codzon' => 'getZonas',
            'codact' => 'getActividades',
            'coddoc' => 'getTipoDocumentos',
            'sexo' => 'getSexos',
            'estciv' => 'getEstadoCivil',
        ];
    }

    protected function aplicarDevolucion(Model $solicitud, string $nota, string $codest, string $camposCorregir): void
    {
        $this->legacy()->devolver($solicitud, $nota, $codest, $camposCorregir);
    }

    protected function aplicarRechazo(Model $solicitud, string $nota, string $codest): void
    {
        $this->legacy()->rechazar($solicitud, $nota, $codest);
    }

    protected function assertAbierta(Model $solicitud): void
    {
        if (in_array((string) $solicitud->getAttribute('estado'), self::ESTADOS_SIN_ACCION, true)) {
            throw new DebugException('La solicitud no admite esta acción por su estado actual.', 422);
        }
    }

    private function deshacerComoDevuelta(Model $solicitud, string $nota, string $codest, bool $sendEmail): void
    {
        $this->aplicarDevolucion($solicitud, $nota, $codest, '');
        if ($sendEmail && $this->notificaPorCorreo()) {
            $this->notifyEmailServices->emailDevolver($solicitud, $this->legacy()->msjDevolver($solicitud, nl2br(e($nota))));
        }
    }

    private function deshacerComoRechazada(Model $solicitud, string $nota, string $codest, bool $sendEmail): void
    {
        $this->aplicarRechazo($solicitud, $nota, $codest);
        if ($sendEmail && $this->notificaPorCorreo()) {
            $this->notifyEmailServices->emailRechazar($solicitud, $this->legacy()->msjRechazar($solicitud, nl2br(e($nota))));
        }
    }

    private function deshacerComoInactiva(Model $solicitud): void
    {
        $solicitud->setAttribute('estado', 'I');
        $solicitud->setAttribute('fecest', date('Y-m-d'));
        $solicitud->save();
    }

    /**
     * @param  array<string, mixed>  $pares
     * @return list<array{label: string, value: string}>
     */
    protected function campos(array $pares): array
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
    protected function etiqueta(array $catalogo, mixed $code): string
    {
        if ($code === null || $code === '') {
            return '';
        }

        return (string) ($catalogo[$code] ?? $code);
    }

    protected function etiquetaCatalogo(string $key, mixed $code): string
    {
        return $this->etiqueta($this->catalogos()[$key] ?? [], $code);
    }

    protected function tipoAportante(Model $solicitud): ?string
    {
        return Mercurio06::query()->where('tipo', $solicitud->getAttribute('tipo'))->value('detalle');
    }

    /**
     * @param  array<string|int, string>  $catalogo
     * @return list<array{value: string, label: string}>
     */
    protected function opciones(array $catalogo): array
    {
        $opciones = [];
        foreach ($catalogo as $value => $label) {
            $opciones[] = ['value' => (string) $value, 'label' => (string) $label];
        }

        return $opciones;
    }

    /**
     * Deja solo los índices que el proceso de aprobación acepta; si el catálogo no cargó, ofrece los códigos.
     *
     * @param  list<array{value: string, label: string}>  $opciones
     * @param  list<string>  $validos
     * @return list<array{value: string, label: string}>
     */
    private function filtrarIndices(array $opciones, array $validos): array
    {
        $filtradas = array_values(array_filter($opciones, fn (array $o) => in_array($o['value'], $validos, true)));
        if ($filtradas !== []) {
            return $filtradas;
        }

        return array_map(fn (string $codigo) => ['value' => $codigo, 'label' => $codigo], $validos);
    }

    /**
     * Campos comunes a la aprobación de personas: datos de pago y fechas.
     *
     * @return list<array<string, mixed>>
     */
    protected function camposPago(): array
    {
        return [
            ['name' => 'tippag', 'label' => 'Tipo medio pago cuota', 'type' => 'select', 'opciones' => $this->opciones($this->pagos()), 'default' => 'T'],
            ['name' => 'codban', 'label' => 'Banco', 'type' => 'select', 'search' => true, 'required_if' => ['field' => 'tippag', 'in' => ['A', 'D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'numcue', 'label' => 'Número cuenta', 'max' => 20, 'required_if' => ['field' => 'tippag', 'in' => ['D']], 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'tipcue', 'label' => 'Tipo cuenta', 'type' => 'select', 'required' => false, 'show_if' => ['field' => 'tippag', 'in' => ['A', 'D']]],
            ['name' => 'giro', 'label' => 'Giro', 'type' => 'select', 'required' => false],
            ['name' => 'codgir', 'label' => 'Motivo no giro', 'type' => 'select', 'required' => false, 'search' => true],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function pagos(): array
    {
        return self::PAGOS;
    }
}
