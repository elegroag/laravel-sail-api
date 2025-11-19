<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use App\Services\Srequest;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Api\ApiSubsidio;

class IndependienteService
{
    private $tipopc = '13';

    private $tipsoc = '08';

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
            $conditions = "and m41.estado NOT IN('I') ";
        } else {
            $conditions = "and m41.estado='{$estado}' ";
        }

        $sql = "SELECT
            m41.*,
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as razsoc,
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as repleg,
            m41.codzon,
            m41.codciu,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m41.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m41.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN m41.estado = 'T' THEN 'Temporal'
                WHEN m41.estado = 'D' THEN 'Devuelto'
                WHEN m41.estado = 'A' THEN 'Aprobado'
                WHEN m41.estado = 'X' THEN 'Rechazado'
                WHEN m41.estado = 'P' THEN 'Pendiente de validación'
                WHEN m41.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            'NATURAL' as tipo_persona,
            m41.coddoc as tipo_documento,
            gener09.detzon as detalle_zona
            FROM mercurio41 as m41
            LEFT JOIN gener09 ON gener09.codzon = m41.codzon
            WHERE m41.documento='{$documento}' and
            m41.coddoc='{$coddoc}' {$conditions}
            ORDER BY m41.fecini ASC;";

        return $this->db->inQueryAssoc($sql);
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEmpresaSubsidio($nit)
    {
        $empresaService = new EmpresaService();
        return $empresaService->buscarEmpresaSubsidio($nit);
    }

    /**
     * archivosRequeridos function
     *
     * @param  Mercurio41  $solicitud
     * @return string
     */
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
            ->where('tipsoc', $this->tipsoc)
            ->orderBy('auto_generado', 'desc')
            ->get();

        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

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
        $html = view('partials/archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ])->render();

        return $html;
    }

    /**
     * dataArchivosRequeridos function
     *
     * @param  Mercurio41  $solicitud
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
            ->where('tipsoc', $this->tipsoc)
            ->orderBy('auto_generado', 'desc')
            ->get();

        foreach ($mercurio14 as $m14) {

            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

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

            $archivo = $m14->getArray();
            $archivo['obliga'] = ($m14->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('I');

        return [
            'disponibles' => $archivos_descargar,
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
     * @return Mercurio41
     */
    public function update($id, $data)
    {
        $independiente = $this->findById($id);
        if ($independiente) {
            $independiente->fill($data);

            return $independiente->save();
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
        $independiente = $this->findById($id);
        if ($independiente) {
            $independiente->fill($data);
            $independiente->setUsuario((new AsignarFuncionario)->asignar($this->tipopc, $this->user['codciu']));

            return $independiente->save();
        }

        return false;
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio41
     */
    public function create($data)
    {
        $independiente = new Mercurio41($data);
        $independiente->setCoddoc($this->user['coddoc']);
        $independiente->setDocumento($this->user['documento']);
        $independiente->setUsuario((new AsignarFuncionario)->asignar($this->tipopc, $this->user['codciu']));
        $independiente->save();
        $id = $independiente->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $independiente;
    }

    /**
     * createByFormData function
     *
     * @param  array  $data
     * @return Mercurio41
     */
    public function createByFormData($data)
    {
        $data['estado'] = 'T';
        $data['log'] = '0';
        $independiente = $this->create($data);

        return $independiente;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio41
     */
    public function findById($id)
    {
        return Mercurio41::where('id', $id)->first();
    }

    public function consultaSeguimiento(int $id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderByDesc('item')
            ->get()
            ->map(function ($seguimiento) {
                $seguimiento->corregir = explode(';', $seguimiento->campos_corregir);

                return $seguimiento;
            });

        return [
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio41)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados(),
        ];
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
        $mercurio41 = $this->findById($id);

        $cm37 = Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->whereIn('coddoc', function ($q) {
                $q->from('mercurio14')
                    ->select('coddoc')
                    ->where('tipopc', $this->tipopc)
                    ->where('tipsoc', $this->tipsoc)
                    ->where('obliga', 'S');
            })
            ->count();

        $cm14 = Mercurio14::where('tipopc', $this->tipopc)
            ->where('tipsoc', $this->tipsoc)
            ->where('obliga', 'S')
            ->count();

        if ($cm37 < $cm14) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio41::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->max('item') + 1;

        $mercurio41->item = $ai;
        $solicitante = Mercurio07::where('documento', $mercurio41->getDocumento())
            ->where('coddoc', $mercurio41->getCoddoc())
            ->where('tipo', $mercurio41->getTipo())
            ->first();

        $mercurio41->repleg = $solicitante->getNombre();
        $mercurio41->razsoc = $solicitante->getNombre();
        $mercurio41->nit = $solicitante->getDocumento();
        $mercurio41->email = $solicitante->getEmail();

        $senderValidationCaja->send($this->tipopc, $mercurio41);
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

        $paramsIndependiente = new ParamsIndependiente;
        $paramsIndependiente->setDatosCaptura($procesadorComando->toArray());

        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );
        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
    }

    public function resumenServicios()
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];
        $tipo = $this->tipo;

        return [
            'afiliacion' => [
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
                    'name' => 'Actualización de  datos',
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
            'productos' => [
                [
                    'name' => 'P. Complemento_nutricional',
                    'url' => 'productos/complemento_nutricional',
                    'imagen' => 'complemento.jpg',
                ],
            ],
            'consultas' => [
                [
                    'name' => 'Consulta Trabajadores',
                    'url' => 'subsidio/consulta_trabajadores_view',
                    'imagen' => 'consulta_trabajadores.jpg',
                ],
                [
                    'name' => 'Consulta de gíro',
                    'url' => 'subsidio/consulta_giro_view',
                    'imagen' => 'consulta_giro.jpg',
                ],
                [
                    'name' => 'Consulta de aportes',
                    'url' => 'subsidio/consulta_aportes_view',
                    'imagen' => 'consulta_aportes.jpg',
                ],
            ],
        ];
    }

    /**
     * buscarTrabajadorSubsidio function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $cedtra
     * @return array|bool
     */
    public function buscarTrabajadorSubsidio($cedtra)
    {
        $procesadorComando = new ApiSubsidio();
        $procesadorComando->send(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]
        );
        $out = $procesadorComando->toArray();
        return ($out['success'] == true) ? $out['data'] : false;
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
                $response["datos"] = Mercurio41::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio41.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio41.*',
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
                $response["datos"] = Mercurio41::whereRaw("usuario='{$usuario}' and estado='P'")->get();
                break;
            case 'count':
                $res = Mercurio41::where("mercurio41.usuario", $usuario)
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) $q->where($condi_extra);
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) $q->whereRaw($condi_extra);
                    })
                    ->get();

                $response["all"] = $res;
                $response["count"] = $res->count();
                break;
            case 'one':
                $response["datos"] = Mercurio41::whereRaw("id='{$numero}' and estado='P'")->first();
                break;
            case 'info':
                $mercurio = Mercurio41::where("id", $numero)->first();
                $response["consulta"] = $this->buscarTrabajadorSubsidio($mercurio->getCedtra());
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}
