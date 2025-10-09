<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Services\Utils\Comman;

class ActualizaEmpresaService
{
    private $tipopc = '5';

    private $user;

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

        if (is_null($estado) || $estado == '') {
            $where = " solis.documento='{$documento}' AND solis.coddoc='{$coddoc}' ";
        } else {
            $where = "solis.documento='{$documento}' AND solis.coddoc='{$coddoc}' AND solis.estado='{$estado}' ";
        }

        $mercurio47 = $this->db->inQueryAssoc("SELECT solis.*,
            (CASE
                WHEN solis.estado = 'T' THEN 'Temporal en edición'
                WHEN solis.estado = 'D' THEN 'Devuelto'
                WHEN solis.estado = 'A' THEN 'Aprobado'
                WHEN solis.estado = 'X' THEN 'Rechazado'
                WHEN solis.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN solis.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle
            FROM mercurio47 as solis
            WHERE {$where}
            ORDER BY solis.id DESC;
        ");

        foreach ($mercurio47 as $ai => $row) {
            $rqs = $this->db->fetchOne("SELECT count(mercurio10.numero) as cantidad
                FROM mercurio10
                LEFT JOIN mercurio47 ON mercurio47.id = mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio47.id ='{$row['id']}'
            ");

            $trayecto = $this->db->fetchOne("SELECT max(mercurio10.item), mercurio10.*
                FROM mercurio10
                LEFT JOIN mercurio47 ON mercurio47.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio47.id ='{$row['id']}' LIMIT 1
            ");

            $mercurio47[$ai] = $row;
            $mercurio47[$ai]['cantidad_eventos'] = $rqs['cantidad'];
            $mercurio47[$ai]['fecha_ultima_solicitud'] = $trayecto['fecsis'];
            $mercurio47[$ai]['estado_detalle'] = (new Mercurio47)->getEstadoInArray($row['estado']);
            $mercurio47[$ai]['tipo_actualizacion_detalle'] = (new Mercurio47)->getTipoActualizacionInArray($row['tipo_actualizacion']);
        }

        return $mercurio47;
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
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );

        $salida = $procesadorComando->toArray();

        return ($salida['success'] == true) ? $salida : false;
    }

    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'DESC')
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
        $html = view('partials.archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ]);

        return $html;
    }

    /**
     * dataArchivosRequeridos function
     *
     * @param  Mercurio47  $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {

        $archivos = [];
        if ($solicitud == false || is_null($solicitud)) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'DESC')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)
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
            $archivo['coddoc'] = $m14->getCoddoc();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('U');

        return [
            'disponibles' => $archivos_descargar,
            'archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ];
    }

    /**
     * loadDisplay function
     *
     * @param  Mercurio47  $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        /* Tag::displayTo("nit", $solicitud->getNit());
        Tag::displayTo("tipdoc", $solicitud->getTipdoc());
        Tag::displayTo("digver", $this->digver($solicitud->getNit()));
        Tag::displayTo("id", $solicitud->getId());
        Tag::displayTo("sigla", $solicitud->getSigla());
        Tag::displayTo("calemp", $solicitud->getCalemp());
        Tag::displayTo("cedrep", $solicitud->getCedrep());
        Tag::displayTo("repleg", $solicitud->getRepleg());
        Tag::displayTo("telefono", $solicitud->getTelefono());
        Tag::displayTo("celular", $solicitud->getCelular());
        Tag::displayTo("email", $solicitud->getEmail());
        Tag::displayTo("fecini", $solicitud->getFeciniString());
        Tag::displayTo("tottra", $solicitud->getTottra());
        Tag::displayTo("valnom", $solicitud->getValnom());
        Tag::displayTo("dirpri", $solicitud->getDirpri());
        Tag::displayTo("ciupri", $solicitud->getCiupri());
        Tag::displayTo("celpri", $solicitud->getCelpri());
        Tag::displayTo("emailpri", $solicitud->getEmailpri());
        Tag::displayTo("prinom", $solicitud->getPrinom());
        Tag::displayTo("segnom", $solicitud->getSegnom());
        Tag::displayTo("priape", $solicitud->getPriape());
        Tag::displayTo("segape", $solicitud->getSegape());
        Tag::displayTo("razsoc", $solicitud->getRazsoc());
        Tag::displayTo("tipper", $solicitud->getTipper());
        Tag::displayTo("matmer", $solicitud->getMatmer());
        Tag::displayTo("direccion", $solicitud->getDireccion());
        Tag::displayTo("tipsoc", $solicitud->getTipsoc());
        Tag::displayTo("codact", $solicitud->getCodact());
        Tag::displayTo("tipemp", $solicitud->getTipemp());
        Tag::displayTo("codcaj", $solicitud->getCodcaj());
        Tag::displayTo("coddocrepleg", $solicitud->getCoddocrepleg()); */
    }

    public function loadDisplaySubsidio($empresa)
    {
        /* Tag::displayTo("tipdoc", $empresa['coddoc']);
        Tag::displayTo("digver", $empresa['digver']);
        Tag::displayTo("nit", $empresa['nit']);
        Tag::displayTo("sigla", $empresa['sigla']);
        Tag::displayTo("calemp", $empresa['calemp']);
        Tag::displayTo("cedrep", $empresa['cedrep']);
        Tag::displayTo("repleg", $empresa['repleg']);
        Tag::displayTo("telefono", $empresa['telefono']);
        Tag::displayTo("email", $empresa['email']);
        Tag::displayTo("tottra", $empresa['tottra']);
        Tag::displayTo("ciupri", $empresa['ciupri']);
        Tag::displayTo("prinom", $empresa['prinom']);
        Tag::displayTo("segnom", $empresa['segnom']);
        Tag::displayTo("priape", $empresa['priape']);
        Tag::displayTo("segape", $empresa['segape']);
        Tag::displayTo("razsoc", $empresa['razsoc']);
        Tag::displayTo("tipper", $empresa['tipper']);
        Tag::displayTo("matmer", $empresa['matmer']);
        Tag::displayTo("direccion", $empresa['direccion']);
        Tag::displayTo("tipsoc", $empresa['tipsoc']);
        Tag::displayTo("codact", $empresa['codact']);
        Tag::displayTo("tipemp", $empresa['tipemp']);
        Tag::displayTo("codcaj", $empresa['codcaj']);
        Tag::displayTo("coddocrepleg", $empresa['coddocrepleg']);
        Tag::displayTo("celular", $empresa['telr']);
        Tag::displayTo("celpri", $empresa['telt']);
        Tag::displayTo("dirpri", $empresa['dirpri']);
        Tag::displayTo("emailpri", $empresa['mailr']); */
    }

    /**
     * update function
     *
     * @param  int  $id
     * @param  array  $data
     * @return Mercurio47
     */
    public function update($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->fill($data);
            $empresa->save();

            return $empresa;
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
        if ($empresa) {
            $empresa->fill($data);

            return $empresa->save();
        } else {
            return false;
        }
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio47
     */
    public function createByFormData($data)
    {
        $data['estado'] = 'T';
        $data['log'] = '0';
        $solicitud = new Mercurio47($data);
        $solicitud->save();

        return $solicitud;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio47
     */
    public function findById($id)
    {
        return Mercurio47::where('id', $id)->first();
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

        $cm37 = (new Mercurio37)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' AND ".
                "numero='{$id}' AND ".
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' and obliga='S')"
        );

        $cm14 = (new Mercurio14)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' and obliga='S'"
        );
        if ($cm37 < $cm14) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio47::where('id', $id)
            ->update([
                'usuario' => (string) $usuario,
                'estado' => 'P',
            ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->max('item') + 1;

        $solicitud->item = $ai;
        $solicitante = Mercurio07::where('documento', $solicitud->getDocumento())
            ->where('coddoc', $solicitud->getCoddoc())
            ->where('tipo', $solicitud->getTipo())
            ->first();

        $solicitud->repleg = $solicitante->getNombre();
        $solicitud->razsoc = $solicitante->getNombre();
        $solicitud->nit = $solicitante->getDocumento();
        $solicitud->email = $solicitante->getEmail();
        $senderValidationCaja->send($this->tipopc, $solicitud);
    }

    public function consultaSeguimiento($id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'DESC')
            ->get();

        foreach ($seguimientos as $ai => $row) {
            $seguimientos[$ai]['corregir'] = explode(';', $row['campos_corregir']);
        }

        return [
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio47)->CamposDisponibles(),
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
}
