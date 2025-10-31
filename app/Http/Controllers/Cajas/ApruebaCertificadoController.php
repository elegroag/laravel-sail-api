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

    protected $tipfun;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user');
        $this->tipfun = session('tipfun');
    }

    /**
     * services variable
     *
     * @var Services
     */
    protected $services;

    public function aplicarFiltro(Request $request, string $estado = 'P')
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

        return response()->json($response);
    }

    public function changeCantidadPagina(Request $request, string $estado = 'P')
    {
        $this->buscar($request, $estado);
    }

    public function index()
    {
        $campo_field = [
            'codben' => 'Cedula',
            'nombre' => 'Primer Apellido',
        ];

        return view('cajas.aprobacioncer.index', [
            'campo_filtro' => $campo_field,
            'filters' => get_flashdata_item('filter_params'),
            'title' => 'Aprueba Certificado',
            'mercurio11' => Mercurio11::all(),
        ]);
    }

    public function buscar(Request $request, string $estado = 'P')
    {
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input('numero', 10);
        $usuario = $this->user['usuario'];

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

        return response()->json($response);
    }

    public function info(Request $request)
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
                'data' => $mercurio45->toArray(),
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

        return response()->json($response);
    }

    /**
     * aprueba function
     *
     * @return void
     */
    public function aprueba(Request $request)
    {
        $this->db->begin();
        try {
            try {
                $aprueba = new ApruebaCertificado;
                $postData = $request->all();
                $idSolicitud = $request->input('id');
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
                    'errors' => $err->render($request),
                ];
            }
        } catch (\Exception $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        return response()->json($salida);
    }

    public function rechazar(Request $request)
    {
        $this->db->begin();
        try {

            $id = $request->input('id');
            $nota = $request->input('nota');
            $codest = $request->input('codest');
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

            $mercurio10->tipopc = $this->tipopc;
            $mercurio10->numero = $id;
            $mercurio10->item = $item;
            $mercurio10->estado = 'X';
            $mercurio10->nota = $nota;
            $mercurio10->codest = $codest;
            $mercurio10->fecsis = $today->format('Y-m-d H:i:s');
            $mercurio10->save();

            $mercurio07 = Mercurio07::whereRaw("tipo='{$mercurio45->getTipo()}' and coddoc='{$mercurio45->getCoddoc()}' and documento = '{$mercurio45->getDocumento()}'")->first();
            $body = 'Certificado rechazado no es valido';

            $senderEmail = new SenderEmail(
                new Srequest([
                    'email_emisor' => $mercurio07->getEmail(),
                    'email_clave' => $mercurio07->getClave(),
                    'asunto' => "Certificado rechazado",
                ])
            );

            $senderEmail->send($mercurio07->getEmail(), $body);
            $this->db->commit();
            $response = [
                'success' => true,
                'msj' => 'El registro se completo con éxito',
            ];
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
                'errors' => $e->render($request),
            ];
        }
        return response()->json($response);
    }
}
