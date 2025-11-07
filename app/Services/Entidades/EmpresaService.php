<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Models\Tranoms;
use App\Services\Srequest;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Api\ApiSubsidio;
use Illuminate\Support\Facades\DB;

class EmpresaService
{
    private $tipopc = '2';  // tipo de solicitud

    private $user;

    private $tipo;

    private $db;

    /**
     * __construct function
     *
     * @param  bool  $init
     * @param  Services  $servicios
     */
    public function __construct()
    {
        $this->user = session('user');
        $this->tipo = session('tipo');
        $this->db = DbBase::rawConnect();
    }

    /**
     * findAllByEstado function
     *
     * @param  string  $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        // usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if (empty($estado)) {
            $conditions = "and solis.estado NOT IN('I') ";
        } else {
            $conditions = "and solis.estado='{$estado}' ";
        }
        $sql = "SELECT solis.*,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and solis.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and solis.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN solis.estado = 'T' THEN 'Temporal en edición'
                WHEN solis.estado = 'D' THEN 'Devuelto'
                WHEN solis.estado = 'A' THEN 'Aprobado'
                WHEN solis.estado = 'X' THEN 'Rechazado'
                WHEN solis.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN solis.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            IF(tipper = 'N', 'NATURAL', 'JURIDICA') as tipo_persona,
            solis.coddoc as tipo_documento,
            gener09.detzon as detalle_zona
            FROM mercurio30 as solis
            LEFT JOIN gener09 ON gener09.codzon = solis.codzon
            WHERE solis.documento='{$documento}' and solis.coddoc='{$coddoc}' {$conditions}
            ORDER BY solis.fecini ASC;";

        $solicitudes = $this->db->inQueryAssoc($sql);

        return $solicitudes;
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     *
     * @param [type] $nit
     * @return array|bool
     */
    public function buscarEmpresaSubsidio($nit)
    {
        if (!$nit) {
            return false;
        }
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        $salida = $procesadorComando->toArray();
        if ($salida['success']) {
            return isset($salida['data']) ? $salida['data'] : false;
        } else {
            return false;
        }
    }

    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $solicitud->getTipsoc())
            ->get();

        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->coddoc)->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m14->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view('empresa/tmp/archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ])->render();

        return $html;
    }

    /**
     * dataArchivosRequeridos function
     *
     * @param  Mercurio30  $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {
        if ($solicitud == false || is_null($solicitud)) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $solicitud->getTipsoc())
            ->orderBy('auto_generado', 'desc')
            ->get();

        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->coddoc)->first();
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m14->coddoc)
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->coddoc, $corregir)) {
                    $corrige = true;
                }
            }

            $archivo = $m14->getArray();
            $archivo['obliga'] = ($m14->obliga == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->detalle);
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->archivo : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();

        return [
            'disponibles' => false,
            'archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ];
    }

    /**
     * update function
     *
     * @param  int  $id
     * @param  array  $data
     * @return Mercurio30
     */
    public function update($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->fill($data);

            return $empresa->save();
        }

        return false;
    }

    /**
     * updateByFormData function
     *
     * @param  int  $id
     * @param  array  $data
     * @return bool
     */
    public function updateByFormData($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            // Usar asignación masiva para actualizar los atributos
            $empresa->fill($data);

            // Establecer el representante legal
            $empresa->setRepleg($data['priape'] . ' ' . $data['segape'] . ' ' . $data['prinom'] . ' ' . $data['segnom']);

            // Asignar funcionario
            $empresa->setUsuario((new AsignarFuncionario)->asignar($this->tipopc, $this->user['codciu']));

            $empresa->setTipo(session('tipo'));
            $empresa->setCoddoc($this->user['coddoc']);
            $empresa->setDocumento($this->user['documento']);

            // Establecer estado y fecha de solicitud
            $empresa->setEstado('T');
            $empresa->setFecsol(date('Y-m-d'));

            return $empresa->save();
        } else {
            return false;
        }
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio30
     */
    public function create($data)
    {
        $empresa = new Mercurio30($data);
        $empresa->setRepleg($data['priape'] . ' ' . $data['segape'] . ' ' . $data['prinom'] . ' ' . $data['segnom']);

        $empresa->setUsuario((new AsignarFuncionario)->asignar($this->tipopc, $this->user['codciu']));

        $empresa->setTipo(session('tipo'));

        $empresa->setCoddoc($this->user['coddoc']);

        $empresa->setDocumento($this->user['documento']);

        $empresa->setMatmer(substr($data['matmer'], 0, 12));

        $empresa->setFax('18001');

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $empresa->id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $empresa->id)->delete();
        Tranoms::where('request', $empresa->id)->delete();

        return $empresa;
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio30
     */
    public function createByFormData($data)
    {
        $empresa = $this->create($data);
        $empresa->setEstado('T');
        $empresa->setLog('0');
        $empresa->save();

        return $empresa;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio30
     */
    public function findById($id)
    {
        return Mercurio30::where('id', $id)->first();
    }

    /**
     * enviarCaja function
     *
     * @param  SenderValidationCaja  $senderValidationCaja
     * @param  int  $id
     * @param  int  $documento
     * @param  int  $coddoc
     * @return void
     */
    public function enviarCaja($senderValidationCaja, $id, $usuario)
    {
        $solicitud = $this->findById($id);
        $tipsoc = $solicitud->getTipsoc();

        $cm37 = Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->whereIn('coddoc', function ($q) use ($tipsoc) {
                $q->from('mercurio14')
                    ->select('coddoc')
                    ->where('tipopc', $this->tipopc)
                    ->where('tipsoc', $tipsoc)
                    ->where('obliga', 'S');
            })
            ->count();

        $cm14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $tipsoc)
            ->where('obliga', 'S')
            ->count();

        if ($cm37 < $cm14) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio30::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->max('item') + 1;

        $solicitud->item = $ai;
        $senderValidationCaja->send($this->tipopc, $solicitud);
    }

    public function consultaSeguimiento($id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->get()
            ->map(function ($row) {
                $campos = explode(';', $row->campos_corregir);
                $row->corregir = $campos;

                return $row;
            })
            ->toArray();

        return [
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio30)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados(),
        ];
    }

    public function digver($mnit)
    {
        $arreglo = [71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
        $nit = sprintf('%015s', $mnit);
        $suma = 0;
        for ($i = 1; $i <= count($arreglo); $i++) {
            $suma += (int) (substr($nit, $i - 1, 1)) * $arreglo[$i - 1];
        }
        $retorno = $suma % 11;
        if ($retorno >= 2) {
            $retorno = 11 - $retorno;
        }

        return $retorno;
    }

    public function addTrabajadoresNomina($tranoms, $id)
    {
        if (! $tranoms) {
            throw new DebugException('Error no hay trabajadores en nomina', 301);
        }
        $cedtras = [];
        foreach ($tranoms as $row) {
            $cedtras[] = $row['cedtra'];

            $tranom = Tranoms::where('request', $id)
                ->where('cedtra', $row['cedtra'])
                ->first();

            if (! $tranom) {
                $tranom = new Tranoms([
                    'cedtra' => sanetizar($row['cedtra']),
                    'nomtra' => sanetizar_input($row['nomtra']),
                    'apetra' => sanetizar_input($row['apetra']),
                    'saltra' => sanetizar($row['saltra']),
                    'fectra' => sanetizar_date($row['fectra']),
                    'cartra' => sanetizar_input($row['cartra']),
                    'request' => $id,
                ]);
                $tranom->save();
            } else {
                $tranom->fill(
                    [
                        'cedtra' => sanetizar($row['cedtra']),
                        'nomtra' => sanetizar_input($row['nomtra']),
                        'apetra' => sanetizar_input($row['apetra']),
                        'saltra' => sanetizar($row['saltra']),
                        'fectra' => sanetizar_date($row['fectra']),
                        'cartra' => sanetizar_input($row['cartra']),
                    ]
                );
                $tranom->save();
            }
        }

        Tranoms::where('request', $id)
            ->whereNotIn('cedtra', $cedtras)
            ->delete();
    }

    public function paramsApi()
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $paramsEmpresa = new ParamsEmpresa;
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());
    }

    public function resumenServicios()
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];
        $tipo = $this->tipo;

        return [
            'afiliacion' => [
                [
                    'name' => 'Solicitudes Trabajadores',
                    'cantidad' => [
                        'pendientes' => Mercurio31::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio31::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio31::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio31::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio31::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'T',
                    'url' => 'trabajador/index',
                    'imagen' => 'trabajadores.jpg',
                ],
                [
                    'name' => 'Solicitudes Cónyuges',
                    'cantidad' => [
                        'pendientes' => Mercurio32::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio32::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio32::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio32::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio32::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'C',
                    'url' => 'conyuge/index',
                    'imagen' => 'conyuges.jpg',
                ],
                [
                    'name' => 'Solicitudes Beneficiarios',
                    'cantidad' => [
                        'pendientes' => Mercurio34::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio34::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio34::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio34::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio34::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'B',
                    'url' => 'beneficiario/index',
                    'imagen' => 'beneficiarios.jpg',
                ],
                [
                    'name' => 'Solicitud Actualiza Datos',
                    'cantidad' => [
                        'pendientes' => Mercurio47::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio47::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio47::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio47::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio47::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'B',
                    'url' => 'actualizadatos/index',
                    'imagen' => 'datos_basicos.jpg',
                ],
            ],
            'productos' => false,
            'consultas' => [
                [
                    'name' => 'Consulta Trabajadores',
                    'url' => 'subsidioemp/consulta_trabajadores_view',
                    'imagen' => 'consulta_trabajadores.jpg',
                ],
                [
                    'name' => 'Consulta de gíro',
                    'url' => 'subsidioemp/consulta_giro_view',
                    'imagen' => 'consulta_giro.jpg',
                ],
                [
                    'name' => 'Consulta de aportes',
                    'url' => 'subsidioemp/consulta_aportes_view',
                    'imagen' => 'consulta_aportes.jpg',
                ],
                [
                    'name' => 'Consulta de nominas',
                    'url' => 'subsidioemp/consulta_nomina_view',
                    'imagen' => 'consulta_aportes.jpg',
                ],
            ],
        ];
    }

    public function consultaTipopc(Srequest $request): array|bool
    {
        $tipo_consulta = $request->getParam('tipo_consulta');
        $tipopc = $request->getParam('tipopc');
        $condi_extra = $request->getParam('condi_extra');
        $usuario = $request->getParam('usuario');
        $numero = $request->getParam('numero');

        switch ($tipo_consulta) {
            case 'all':
                $response["datos"] = Mercurio30::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio30.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio30.*',
                        'mercurio10.estado as estado',
                        'mercurio10.fecsis as fecest',
                    ])
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) $q->where($condi_extra);
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) $q->whereRaw($condi_extra);
                    })
                    ->get();
                break;
            case 'alluser':
                $response["datos"] = Mercurio30::where("usuario", $usuario)->where("estado", 'P')->get();
                break;
            case 'count':
                $res = Mercurio30::where("mercurio30.usuario", $usuario)
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) $q->where($condi_extra);
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) $q->whereRaw($condi_extra);
                    })
                    ->get();

                $response["count"] = $res->count();
                $response["all"] = $res;
                break;
            case 'one':
                $response["datos"] = Mercurio30::where("id", $numero)->where("estado", 'P')->first();
                break;
            case 'info':
                $mercurio = Mercurio30::where("id", $numero)->first();
                $response["consulta"] = $this->buscarEmpresaSubsidio($mercurio->getNit());
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}
