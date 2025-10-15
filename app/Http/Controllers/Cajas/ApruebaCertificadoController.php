<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Gener42;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio45;
use App\Services\Aprueba\ApruebaCertificado;
use App\Services\CajaServices\CertificadosServices;
use App\Services\Srequest;
use App\Services\Utils\Pagination;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApruebaCertificadoController extends ApplicationController
{
    protected $tipopc = 8;

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];

        $pagination = new Pagination(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'query' => "usuario='{$usuario}' and estado='{$estado}'",
                    'estado' => $estado,
                ]
            )
        );

        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_certificado', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices);

        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request, string $estado = 'P')
    {
        $this->buscarAction($request, $estado);
    }

    public function indexAction()
    {
        $campo_field = [
            'codben' => 'Cedula',
            'nombre' => 'Primer Apellido',
        ];

        return view('cajas.aprobacioncer.index', [
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Certificado',
            'buttons' => ['F'],
            'mercurio11' => Mercurio11::all(),
        ]);
    }

    public function buscarAction(Request $request, string $estado = 'P')
    {
        $this->setResponse('ajax');
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = parent::getActUser();

        $pagination = new Pagination(
            new Srequest(
                [
                    'cantidadPaginas' => $cantidad_pagina,
                    'query' => "usuario='{$usuario}' and estado='{$estado}'",
                    'estado' => $estado,
                    'pagina' => $pagina,
                ]
            )
        );

        $query = $pagination->filter(
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata('filter_certificado', $query, true);
        set_flashdata('filter_params', $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices);

        return $this->renderObject($response, false);
    }

    public function inforAction(Request $request)
    {
        try {
            $id = $request->input('id');
            if (! $id) {
                throw new DebugException('Error no se puede identificar el identificador de la solicitud.', 501);
            }
            $mercurio45 = Mercurio45::where("id", $id)->first();
            $html = view(
                'cajas/aprobacioncer/tmp/consulta',
                [
                    'mercurio01' => Mercurio01::first(),
                    'mercurio45' => $mercurio45,
                ]
            )->render();

            $certificadoServices = new CertificadosServices;
            $adjuntos = $certificadoServices->adjuntos($mercurio45);
            $seguimiento = $certificadoServices->seguimiento($mercurio45);

            $campos_disponibles = $mercurio45->CamposDisponibles();
            $response = [
                'success' => true,
                'data' => $mercurio45->getArray(),
                'mercurio11' => Mercurio11::all(),
                'consulta' => $html,
                'adjuntos' => $adjuntos,
                'seguimiento' => $seguimiento,
                'campos_disponibles' => $campos_disponibles,
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    /**
     * apruebaAction function
     *
     * @return void
     */
    public function apruebaAction(Request $request)
    {
        $this->setResponse('ajax');

        $user = session()->get('user');
        $debuginfo = [];
        try {
            try {
                $acceso = (new Gener42)->count('*', "conditions: permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject([
                        'success' => false,
                        'msj' => 'El usuario no dispone de permisos de aprobación',
                    ]);
                }

                $aprueba = new ApruebaCertificado;
                $this->db->begin();
                $postData = $request->all();
                $idSolicitud = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $this->db->commit();
                $aprueba->enviarMail($request->input('actapr'));
                $salida = [
                    'success' => true,
                    'msj' => 'El registro se completo con éxito',
                ];
            } catch (DebugException $err) {

                $this->db->rollback();
                $salida = [
                    'success' => false,
                    'msj' => $err->getMessage(),
                ];
            }
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        if ($debuginfo) {
            $salida['info'] = $debuginfo;
        }

        return $this->renderObject($salida, false);
    }

    public function rechazarAction(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $nota = $request->input('nota', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $codest = $request->input('codest', 'addslaches', 'alpha', 'extraspaces', 'striptags');
            $modelos = ['mercurio10', 'mercurio45'];

            $response = $this->db->begin();
            $today = Carbon::now();
            $mercurio45 = Mercurio45::where("id", $id)->first();
            $mercurio45->update([
                "estado" => "X",
                "motivo" => $nota,
                "codest" => $codest,
                "fecest" => $today->format('Y-m-d H:i:s'),
            ]);
            $item = Mercurio10::whereRaw("tipopc='{$this->tipopc}' and numero='{$id}'")->max('item') + 1;
            $mercurio10 = new Mercurio10;

            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado('X');
            $mercurio10->setNota($nota);
            $mercurio10->setCodest($codest);
            $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));
            if (! $mercurio10->save()) {

                $this->db->rollback();
            }
            $mercurio07 = Mercurio07::whereRaw("tipo='{$mercurio45->getTipo()}' and coddoc='{$mercurio45->getCoddoc()}' and documento = '{$mercurio45->getDocumento()}'")->first();
            $asunto = 'Certificado';
            $msj = 'acabas de utilizar';
            $senderEmail = new SenderEmail(
                new Srequest([
                    'email_emisor' => $mercurio07->getEmail(),
                    'email_clave' => $mercurio07->getClave(),
                    'asunto' => $asunto,
                ])
            );

            $senderEmail->send($mercurio07->getEmail(), $asunto);
            $this->db->commit();
            $response = parent::successFunc('Movimiento Realizado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se pudo realizar el movimiento');

            return $this->renderObject($response, false);
        }
    }
}
