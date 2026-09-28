<?php

namespace App\Services\CajaServices;

use App\Exceptions\DebugException;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio39;
use App\Models\Mercurio41;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Edición de la información registrada de solicitudes de afiliación en estado pendiente.
 */
class EditarSolicitudService
{
    public const ESTADO_EDITABLE = 'P';

    /**
     * Modelo y lista blanca de campos editables por tipopc.
     * Los campos de identificación del titular, estado y trazabilidad quedan excluidos.
     */
    private const FLUJOS = [
        '2' => [
            'model' => Mercurio30::class,
            'campos' => [
                'razsoc', 'sigla', 'digver', 'calemp', 'cedrep', 'repleg', 'direccion', 'codciu', 'codzon',
                'telefono', 'celular', 'fax', 'email', 'codact', 'fecini', 'tottra', 'valnom', 'tipsoc',
                'dirpri', 'ciupri', 'telpri', 'celpri', 'emailpri', 'tipemp', 'tipper', 'prinom', 'segnom',
                'priape', 'segape', 'matmer', 'coddocrepleg', 'priaperepleg', 'segaperepleg', 'prinomrepleg',
                'segnomrepleg', 'barnotif', 'barcomer',
            ],
        ],
        '1' => [
            'model' => Mercurio31::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'orisex', 'estciv', 'cabhog',
                'codciu', 'codzon', 'direccion', 'barrio', 'telefono', 'celular', 'fax', 'email', 'fecing',
                'salario', 'tipsal', 'captra', 'tipdis', 'nivedu', 'rural', 'horas', 'tipcon', 'trasin',
                'vivienda', 'tipafi', 'profesion', 'cargo', 'autoriza', 'facvul', 'peretn', 'dirlab', 'ciulab',
                'ruralt', 'comision', 'tipjor', 'codsuc', 'tippag', 'numcue', 'otra_empresa', 'resguardo_id',
                'pub_indigena_id', 'codban', 'tipcue',
            ],
        ],
        '3' => [
            'model' => Mercurio32::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'estciv', 'comper', 'tiecon',
                'ciures', 'codzon', 'tipviv', 'direccion', 'barrio', 'telefono', 'celular', 'email', 'nivedu',
                'fecing', 'codocu', 'salario', 'captra', 'tipsal', 'tippag', 'numcue', 'empresalab',
                'resguardo_id', 'pub_indigena_id', 'codban', 'tipcue', 'tipdis', 'peretn', 'zoneurbana',
            ],
        ],
        '4' => [
            'model' => Mercurio34::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'parent', 'huerfano', 'tiphij',
                'nivedu', 'captra', 'tipdis', 'calendario', 'cedacu', 'resguardo_id', 'pub_indigena_id', 'peretn',
                'celular', 'codban', 'tipcue', 'numcue', 'tippag', 'biocedu', 'biotipdoc', 'bioprinom',
                'biosegnom', 'biopriape', 'biosegape', 'bioemail', 'biophone', 'biocodciu', 'biodire',
                'biourbana', 'biodesco',
            ],
        ],
        '13' => [
            'model' => Mercurio41::class,
            'campos' => [
                'calemp', 'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'estciv', 'cabhog',
                'codciu', 'codzon', 'direccion', 'barrio', 'telefono', 'celular', 'email', 'fecini', 'salario',
                'captra', 'tipdis', 'nivedu', 'rural', 'vivienda', 'tipafi', 'autoriza', 'codact',
                'coddocrepleg', 'peretn', 'resguardo_id', 'pub_indigena_id', 'facvul', 'orisex', 'tippag',
                'numcue', 'cargo', 'codban', 'tipcue', 'dirlab', 'ciulab', 'tipper',
            ],
        ],
        '9' => [
            'model' => Mercurio38::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'estciv', 'cabhog', 'codciu',
                'codzon', 'direccion', 'barrio', 'telefono', 'celular', 'email', 'fecing', 'salario', 'captra',
                'tipdis', 'nivedu', 'rural', 'vivienda', 'tipafi', 'autoriza', 'codact', 'calemp', 'orisex',
                'fecini', 'tipsoc', 'tipemp', 'coddocrepleg', 'tipper', 'resguardo_id', 'pub_indigena_id',
                'facvul', 'peretn', 'codban', 'tipcue', 'tippag', 'cargo', 'numcue',
            ],
        ],
        '10' => [
            'model' => Mercurio36::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'estciv', 'cabhog', 'codciu',
                'codzon', 'direccion', 'barrio', 'telefono', 'celular', 'email', 'fecini', 'salario', 'captra',
                'tipdis', 'nivedu', 'rural', 'vivienda', 'tipafi', 'autoriza', 'codact', 'calemp', 'facvul',
                'peretn', 'orisex', 'codban', 'tipcue', 'tippag', 'cargo', 'numcue', 'coddocrepleg',
                'resguardo_id', 'pub_indigena_id',
            ],
        ],
        '11' => [
            'model' => Mercurio39::class,
            'campos' => [
                'priape', 'segape', 'prinom', 'segnom', 'fecnac', 'ciunac', 'sexo', 'estciv', 'cabhog', 'codciu',
                'codzon', 'direccion', 'barrio', 'telefono', 'celular', 'fax', 'email', 'fecing', 'salario',
                'captra', 'tipdis', 'nivedu', 'rural', 'vivienda', 'tipafi', 'autoriza', 'codact', 'calemp',
            ],
        ],
    ];

    private const CAMPOS_EMAIL = ['email', 'emailpri', 'bioemail'];

    private const CAMPOS_FECHA = ['fecnac', 'fecing', 'fecini'];

    private const CAMPOS_NUMERICOS = ['salario', 'tottra', 'valnom', 'horas'];

    /**
     * Campos editables del flujo.
     *
     * @param  string  $tipopc  Tipo de opción del flujo de afiliación.
     * @return array<int, string>
     *
     * @throws DebugException Si el flujo no admite edición.
     */
    public function campos(string $tipopc): array
    {
        return $this->flujo($tipopc)['campos'];
    }

    /**
     * Reglas de validación para los campos editables del flujo.
     *
     * @param  string  $tipopc  Tipo de opción del flujo de afiliación.
     * @return array<string, string>
     */
    public function rules(string $tipopc): array
    {
        $rules = ['id' => 'required|integer'];
        foreach ($this->campos($tipopc) as $campo) {
            $rules[$campo] = match (true) {
                in_array($campo, self::CAMPOS_EMAIL, true) => 'nullable|email|max:120',
                in_array($campo, self::CAMPOS_FECHA, true) => 'nullable|date',
                in_array($campo, self::CAMPOS_NUMERICOS, true) => 'nullable|numeric',
                default => 'nullable|string|max:255',
            };
        }

        return $rules;
    }

    /**
     * Datos actuales de una solicitud que puede editarse.
     *
     * @param  string  $tipopc  Tipo de opción del flujo de afiliación.
     * @param  int  $id  Identificador de la solicitud.
     *
     * @throws DebugException Si la solicitud no existe o no está pendiente.
     */
    public function solicitudEditable(string $tipopc, int $id): array
    {
        $solicitud = $this->flujo($tipopc)['model']::where('id', $id)->first();
        $this->validarEditable($solicitud);

        return $solicitud->toArray();
    }

    /**
     * Actualiza los campos editables de una solicitud pendiente y registra el seguimiento.
     *
     * @param  string  $tipopc  Tipo de opción del flujo de afiliación.
     * @param  int  $id  Identificador de la solicitud.
     * @param  array  $data  Datos validados recibidos del formulario.
     * @param  string  $usuario  Funcionario que realiza la edición.
     * @return array{cambios: array<int, string>, data: array}
     *
     * @throws DebugException Si la solicitud no existe o no está pendiente.
     */
    public function editar(string $tipopc, int $id, array $data, string $usuario): array
    {
        $flujo = $this->flujo($tipopc);

        return DB::transaction(function () use ($flujo, $tipopc, $id, $data, $usuario) {
            $solicitud = $flujo['model']::where('id', $id)->lockForUpdate()->first();
            $this->validarEditable($solicitud);

            $cambios = [];
            foreach ($flujo['campos'] as $campo) {
                if (! array_key_exists($campo, $data)) {
                    continue;
                }
                $valor = is_string($data[$campo]) ? trim($data[$campo]) : $data[$campo];
                if ($valor === null || $valor === '') {
                    continue;
                }
                if ((string) $solicitud->getRawOriginal($campo) !== (string) $valor) {
                    $cambios[$campo] = $valor;
                }
            }

            if (! $cambios) {
                return ['cambios' => [], 'data' => $solicitud->toArray()];
            }

            $flujo['model']::where('id', $id)->update($cambios);

            $item = (int) Mercurio10::where('tipopc', $tipopc)->where('numero', $id)->max('item') + 1;
            Mercurio10::create([
                'tipopc' => $tipopc,
                'numero' => $id,
                'item' => $item,
                'estado' => self::ESTADO_EDITABLE,
                'nota' => mb_substr(
                    "Edición de información registrada por {$usuario}. Campos: ".implode(', ', array_keys($cambios)),
                    0,
                    800
                ),
                'codest' => null,
                'fecsis' => Carbon::now()->format('Y-m-d'),
            ]);

            return [
                'cambios' => array_keys($cambios),
                'data' => $flujo['model']::where('id', $id)->first()->toArray(),
            ];
        });
    }

    private function validarEditable($solicitud): void
    {
        if (! $solicitud) {
            throw new DebugException('La solicitud no está disponible para editar', 404);
        }

        if ($solicitud->estado !== self::ESTADO_EDITABLE) {
            throw new DebugException('Solo se pueden editar solicitudes pendientes', 422);
        }
    }

    private function flujo(string $tipopc): array
    {
        if (! isset(self::FLUJOS[$tipopc])) {
            throw new DebugException('El tipo de solicitud no admite edición', 422);
        }

        return self::FLUJOS[$tipopc];
    }
}
