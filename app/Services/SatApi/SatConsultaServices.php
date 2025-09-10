<?php

namespace App\Services\SatApi;

use App\Services\Utils\Comman;

class SatConsultaServices
{
    public function consultaSeguimientoSat($tipopc, $sat)
    {
        $response = "";
        $response .= "<hr class='my-3'>";
        $response .= "<div class='row' style='overflow:scroll'>";
        $response .= "<table class='table table-bordered table-hover'>";
        $response .= "<thead>";
        $response .= "<tr>";
        $response .= "<th colspan=3>Seguimiento</th>";
        $response .= "</tr>";
        $response .= "<tr>";
        $response .= "<th>ID</th>";
        $response .= "<th>Usuario</th>";
        $response .= "<th>Tipo Tramite</th>";
        $response .= "<th>Gestion</th>";
        $response .= "</tr>";
        $response .= "</thead>";
        $response .= "<tbody>";

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'datosSat20', 'params' => $_POST));
        $sat20 = $apiRest->toArray();
        $sat20 = $sat20["data"];

        if (!empty($sat20)) {

            $apiRest = Comman::Api();
            $apiRest->runCli(0, array('servicio' => 'datosSat20', 'params' => $_POST));
            $sat20 = $apiRest->toArray();

            $sat20 = $sat20["data"]["info"];
            $item = 0;
            foreach ($sat20 as $msat20) {
                $item++;
                $response .= "<tr>";
                $response .= "<td>{$item}</td>";
                $response .= "<td>{$msat20["nombre"]}</td>";
                $response .= "<td>{$msat20["tiptra"]}</td>";
                $response .= "<td>{$msat20["gestion"]}</td>";

                $response .= "</tr>";
                $response .= "<tr>";
                $response .= "<td>Fecha</td>";
                $response .= "<td colspan='4'>{$msat20["fecha"]}</td>";
                $response .= "</tr>";

                $response .= "</tr>";
                $response .= "<tr>";
                $response .= "<td>Estado</td>";
                $response .= "<td colspan='4'>{$msat20["estado"]}</td>";
                $response .= "</tr>";

                $response .= "</tr>";
                $response .= "<tr>";
                $response .= "<td style='border-bottom:1px solid'>Nota</td>";
                $response .= "<td style='border-bottom:1px solid' colspan='4'>{$msat20["nota"]}</td>";
                $response .= "</tr>";
            }
        } else {
            $response .= "<tr>";
            $response .= "<td colspan=2>NO HAY DATOS DE SEGUIMIENTO</td>";
            $response .= "</tr>";
        }
        $response .= "</tbody>";
        $response .= "</table>";
        $response .= "</div>";
        return $response;
    }

    public function consultaEmpresaSat($sat02)
    {
        $tipopc = 2;
        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }

        $col = "<div class='col-md-3 border-top border-right'>";
        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";
        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nit</label>";
        $response .= "<p class='pl-2 description'>{$sat02['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Razsoc</label>";
        $response .= "<p class='pl-2 description'>{$sat02['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Persona</label>";
        $response .= "<p class='pl-2 description'>{$sat02['tipper']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat02['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat02["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Solicitud</label>";
        $response .= "<p class='pl-2 description'>{$sat02['fecsol']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Afiliación</label>";
        $response .= "<p class='pl-2 description'>{$sat02['fecafi']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Dirección</label>";
        $response .= "<p class='pl-2 description'>{$sat02['direccion']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$sat02['telefono']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$sat02['email']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Representante</label>";
        $response .= "<p class='pl-2 description'>{$sat02['numdocrep']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre Representante</label>";
        $response .= "<p class='pl-2 description'>{$sat02['nombrer']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat02['resultado']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat02['mensaje']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat02['codigo']}</p>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= $this->consultaSeguimientoSat($tipopc, $sat02);
        return $response;
    }

    public function consultaEmpresaSat2($sat03)
    {
        $tipopc = 2;
        //$mercurio01 = $this->Mercurio01->findFirst();
        //$mercurio37 = $this->Mercurio37->find("tipopc = '$tipopc' and numero = '{$mercurio30->getId()}'");

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nit</label>";
        $response .= "<p class='pl-2 description'>{$sat03['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Razsoc</label>";
        $response .= "<p class='pl-2 description'>{$sat03['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Persona</label>";
        $response .= "<p class='pl-2 description'>{$sat03['tipper']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat03['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat03["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Solicitud</label>";
        $response .= "<p class='pl-2 description'>{$sat03['fecsol']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Afiliación</label>";
        $response .= "<p class='pl-2 description'>{$sat03['fecafi']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Dirección</label>";
        $response .= "<p class='pl-2 description'>{$sat03['direccion']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$sat03['telefono']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$sat03['email']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Representante</label>";
        $response .= "<p class='pl-2 description'>{$sat03['numdocrep']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre Representante</label>";
        $response .= "<p class='pl-2 description'>{$sat03["nombrer"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat03["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat03["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat03["codigo"]}</p>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= $this->consultaSeguimientoSat($tipopc, $sat03);
        return $response;
    }

    public function consultaPerdidagrave($sat08)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();
        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat08['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Persona</label>";
        $response .= "<p class='pl-2 description'>{$sat08['tipper']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Documento Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat08['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Documento Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat08['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Serial Sat</label>";
        $response .= "<p class='pl-2 description'>{$sat08["sersat"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Perdida Afiliacion</label>";
        $response .= "<p class='pl-2 description'>{$sat08["fecper"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Razon Social</label>";
        $response .= "<p class='pl-2 description'>{$sat08["razsoc"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Departamento</label>";
        $response .= "<p class='pl-2 description'>{$sat08["coddep"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Causa</label>";
        $response .= "<p class='pl-2 description'>{$sat08["causa"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat08["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Estado</label>";
        $response .= "<p class='pl-2 description'>{$sat08["estado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat08["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat08["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat08["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat08);
        return $response;
    }

    public function consultaResafiliacion($sat05)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat05['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat05['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Respuesta Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat05["restra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat05['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Afiliación</label>";
        $response .= "<p class='pl-2 description'>{$sat05['fecafi']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Motivo</label>";
        $response .= "<p class='pl-2 description'>{$sat05["motivo"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat05["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat05["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat05["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat05);
        return $response;
    }

    public function consultaDesafiliaciones($sat06)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat06['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat06['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat06['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Solicitud</label>";
        $response .= "<p class='pl-2 description'>{$sat06['fecsol']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Desafiliación</label>";
        $response .= "<p class='pl-2 description'>{$sat06["fecdes"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Paz y Salvo</label>";
        $response .= "<p class='pl-2 description'>{$sat06["pazsal"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Paz y Salvo</label>";
        $response .= "<p class='pl-2 description'>{$sat06["fecpazsal"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat06["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat06["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat06["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat06);
        return $response;
    }

    public function consultaResdesafiliacion($sat07)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat07['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat07['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat07['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Respuesta</label>";
        $response .= "<p class='pl-2 description'>{$sat07["fecres"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Desafiliación</label>";
        $response .= "<p class='pl-2 description'>{$sat07["fecdes"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Motivo</label>";
        $response .= "<p class='pl-2 description'>{$sat07["motivo"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Paz y Salvo</label>";
        $response .= "<p class='pl-2 description'>{$sat07["pazsal"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Paz y Salvo</label>";
        $response .= "<p class='pl-2 description'>{$sat07["fecpaz"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat07["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat07["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat07["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat07);
        return $response;
    }

    public function consultaRelacionlab($sat09)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat09['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat09['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat09['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat09["numdoctra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Inicio</label>";
        $response .= "<p class='pl-2 description'>{$sat09["fecini"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat09["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Nacimiento</label>";
        $response .= "<p class='pl-2 description'>{$sat09["fecnac"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Sexo</label>";
        $response .= "<p class='pl-2 description'>{$sat09["sexo"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Dirección</label>";
        $response .= "<p class='pl-2 description'>{$sat09['direccion']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Telefono</label>";
        $response .= "<p class='pl-2 description'>{$sat09['telefono']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Email</label>";
        $response .= "<p class='pl-2 description'>{$sat09['email']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$sat09["salario"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Salario</label>";
        $response .= "<p class='pl-2 description'>{$sat09["tipsal"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Horas Trabajadas</label>";
        $response .= "<p class='pl-2 description'>{$sat09["hortra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat09["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat09["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat09["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat09);
        return $response;
    }

    public function consultaTerminolab($sat10)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat10['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat10['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat10['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat10["numdoctra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Terminación</label>";
        $response .= "<p class='pl-2 description'>{$sat10["fecter"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat10["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat10["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat10["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat10["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat10);
        return $response;
    }

    public function consultaSuspension($sat11)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat11['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat11['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat11['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat11["numdoctra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Inicio</label>";
        $response .= "<p class='pl-2 description'>{$sat11["fecini"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Final</label>";
        $response .= "<p class='pl-2 description'>{$sat11["fecfin"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Novedad</label>";
        $response .= "<p class='pl-2 description'>{$sat11["indnov"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat11["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat11["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat11["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat11["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat11);
        return $response;
    }

    public function consultaLicenciaryn($sat12)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat12['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat12['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat12['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat12["numdoctra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Inicio</label>";
        $response .= "<p class='pl-2 description'>{$sat12["fecini"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Final</label>";
        $response .= "<p class='pl-2 description'>{$sat12["fecfin"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Novedad</label>";
        $response .= "<p class='pl-2 description'>{$sat12["indnov"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat12["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat12["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat12["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat12["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat12);
        return $response;
    }

    public function consultaModsalario($sat13)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat13['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Persona</label>";
        $response .= "<p class='pl-2 description'>{$sat13['tipper']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo de Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat13['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat13['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Documento Trabajador</label>";
        $response .= "<p class='pl-2 description'>{$sat13["numdoctra"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Modificación</label>";
        $response .= "<p class='pl-2 description'>{$sat13["fecmod"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Salario</label>";
        $response .= "<p class='pl-2 description'>{$sat13["salario"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Salario</label>";
        $response .= "<p class='pl-2 description'>{$sat13["tipsal"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Nombre</label>";
        $response .= "<p class='pl-2 description'>{$sat13["nombre"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat13["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat13["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat13["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat13);
        return $response;
    }

    public function consultaRetirodef($sat14)
    {
        $tipopc = 2;

        $apiRest = Comman::Api();
        $apiRest->runCli(0, array('servicio' => 'captura_empresa', 'params' => array()));
        $datos_captura = $apiRest->toArray();

        if ($datos_captura['flag'] == true) {
            $datos_captura = $datos_captura['data'];
            $_coddoc = array();
            foreach ($datos_captura['coddoc'] as $data) {
                $_coddoc[$data['coddoc']] = $data['detalle'];
            }
            $_calemp = array();
            foreach ($datos_captura['calemp'] as $data) $_calemp[$data['calemp']] = $data['detalle'];
            $_codciu = array();
            foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
            $_codzon = array();
            foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
            $_codact = array();
            foreach ($datos_captura['codact'] as $data) $_codact[$data['codact']] = $data['detalle'];
            $_tipsoc = array();
            foreach ($datos_captura['tipsoc'] as $data) $_tipsoc[$data['tipsoc']] = $data['detalle'];
        }


        $col = "<div class='col-md-3 border-top border-right'>";

        $response = "";
        $response .= "<h6 class='heading-small text-muted mb-4'>Datos Empresa</h6>";

        $response .= "<div class='row pl-lg-4 pb-3'>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Transacción</label>";
        $response .= "<p class='pl-2 description'>{$sat14['numtraccf']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Tipo Documento Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat14['tipdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Numero Empresa</label>";
        $response .= "<p class='pl-2 description'>{$sat14['numdocemp']}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Fecha Retiro</label>";
        $response .= "<p class='pl-2 description'>{$sat14["fecret"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Causa Retiro</label>";
        $response .= "<p class='pl-2 description'>{$sat14["cauret"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Resultado</label>";
        $response .= "<p class='pl-2 description'>{$sat14["resultado"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Mensaje</label>";
        $response .= "<p class='pl-2 description'>{$sat14["mensaje"]}</p>";
        $response .= "</div>";
        $response .= $col;
        $response .= "<label class='form-control-label'>Codigo</label>";
        $response .= "<p class='pl-2 description'>{$sat14["codigo"]}</p>";
        $response .= "</div>";

        $response .= "</div>";
        /*
		$response .= "<hr class='my-3'>";
		$response .= "<h6 class='heading-small text-muted mb-4'>Archivos</h6>";
		$response .= "<div class='row pl-lg-4'>";


        foreach($mercurio37 as $mmercurio37){
            $mercurio12 = $this->Mercurio12->findFirst("coddoc='{$mmercurio37->getCoddoc()}'");
			$response .= "<div class='col-md-4 mb-2'>";
			$response .= "<button class='btn btn-icon btn-block btn-outline-default' type='button' onclick=\"verArchivo('{$mercurio01->getPath()}','{$mmercurio37->getArchivo()}')\">";
			$response .= "<span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>";
			$response .= "<span class='btn-inner--text'>{$mercurio12->getDetalle()}</span>";
			$response .= "</button>";
			$response .= "</div>";
        }
		$response .= "</div>";	*/
        $response .= $this->consultaSeguimientoSat($tipopc, $sat14);
        return $response;
    }

    public function sendWebServiceSat($tabla, $data, $codapl = 'SA')
    {
        /* $response = array();
        $modelo = $this->convertirModeloTabla($tabla);

        try {

            $sat01 = $this->Sat01->findFirst("codapl='{$codapl}'");
            $sat29 = $this->Sat29->findFirst("referencia='{$tabla}' AND codapl='{$codapl}'");
            if ($sat29) {
                $msat20 = $this->Sat20->findFirst("numtraccf = {$data['NumeroRadicadoSolicitud']}");
                $msat20->setEstado('E');
                $msat20->setNota('Enviado al SAT');
                if (!$msat20->save()) {
                    $Transaccion->rollback(implode(",", $msat20->getMessages()));
                }
                $token = $this->tokenSat(trim($sat01->getPath()), trim($sat01->getNit()), trim($sat01->getPassword()), trim($sat01->getGrantType()), trim($sat29->getClientId()));
                $url = trim($sat01->getPath()) . trim($sat29->getNomsersat());
                $ch = curl_init();
                $headers  = array(
                    "Authorization: $token",
                    'Content-Type: application/json'
                );
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result     = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $resultado = json_decode($result, true);
                if (isset($data['NumeroRadicadoSolicitud'])) {
                    $msat20 = $this->Sat20->findFirst("numtraccf = {$data['NumeroRadicadoSolicitud']}");
                    if (isset($resultado['codigo'])) {
                        if ($resultado['codigo'] == 200) {
                            $msat20->setEstado('O');
                        } elseif (substr($resultado['codigo'], 0, 1) == 'A') {
                            $msat20->setEstado('U');
                        } else {
                            $msat20->setEstado('G');
                        }
                        $msat20->setNota($resultado['mensaje']);
                    } else {
                        $msat20->setEstado('X');
                        if (isset($resultado['Message'])) {
                            $msat20->setNota($resultado['Message']);
                        } else {
                            $msat20->setNota('Error no identificado');
                        }
                    }
                    if (!$msat20->save()) {
                        $Transaccion->rollback(implode(",", $msat20->getMessages()));
                    } //TODO posible mensaje log en caso de no guardar
                    $modelo = $this->convertirModeloTabla($tabla);
                    $model = $this->$modelo->findFirst("numtraccf = '{$data['NumeroRadicadoSolicitud']}'");
                    if (isset($resultado['resultado']) && !empty($resultado['resultado'])) {
                        $model->setResultado($resultado['resultado']);
                    }
                    if (isset($resultado['mensaje']) && !empty($resultado['mensaje'])) {
                        $model->setMensaje($resultado['mensaje']);
                    }
                    if (isset($resultado['codigo']) && !empty($resultado['codigo'])) {
                        $model->setCodigo($resultado['codigo']);
                    }
                    if (isset($resultado['Message']) && !empty($resultado['Message'])) {
                        $model->setCodigo($statusCode);
                        $model->setMensaje($resultado['Message']);
                    }
                    if (!$model->save()) {
                        $Transaccion->rollback(implode(",", $model->getMessages()));
                    } //TODO posible mensaje log en caso de no guardar
                } else {
                    $Transaccion->rollback("Error campo 'NumeroRadicadoSolicitud' no existe");
                    //TODO posible mensaje log
                }
                $Transaccion->commit();
                if ($statusCode == 200) {
                    $response['flag'] = true;
                    $response['statusCode'] = $statusCode;
                    $response['data'] = $resultado;
                } else {
                    $response['flag'] = false;
                    $response['statusCode'] = $statusCode;
                    $response['msg'] = $resultado;
                }
            } else {
                $response['flag'] = false;
                $response['statusCode'] = 500;
                $response['msg'] = "El servicio no esta configurado";
            }
            return json_encode($response);
        } catch (TransactionFailed $tf) {
            $response['flag'] = false;
            $response['statusCode'] = 500;
            $response['msg'] = $tf->getMessage();
            return json_encode($response);
        } */
    }

    public function getNameFieldSAT($tabla = '')
    {
        $response = array();
        if ($tabla == "sat02") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipper'] = "TipoPersona";
            $response['tipemp'] = "NaturalezaJuridicaEmpleador";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['prinom'] = "PrimerNombreEmpleador";
            $response['segnom'] = "SegundoNombreEmpleador";
            $response['priape'] = "PrimerApellidoEmpleador";
            $response['segape'] = "SegundoApellidoEmpleador";
            $response['fecsol'] = "FechaSolicitud";
            $response['perafigra'] = "PerdidaAfiliacionCausaGrave";
            $response['fecafi'] = "FechaEfectivaAfiliacion";
            $response['razsoc'] = "RazonSocial";
            $response['matmer'] = "NumeroMatriculaMercantil";
            $response['coddep'] = "Departamento";
            $response['codmun'] = "Municipio";
            $response['direccion'] = "DireccionContacto";
            $response['telefono'] = "NumeroTelefono";
            $response['email'] = "CorreoElectronico";
            $response['tipdocrep'] = "TipoDocumentoRepresentante";
            $response['numdocrep'] = "NumeroDocumentoRepresentante";
            $response['prinom2'] = "PrimerNombreRepresentante";
            $response['segnom2'] = "SegundoNombreRepresentante";
            $response['priape2'] = "PrimerApellidoRepresentante";
            $response['segape2'] = "SegundoApellidoRepresentante";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
            $response['noafissfant'] = "Manifestacion";
        }
        //afiliacion no primera vez
        if ($tabla == "sat03") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTrans accion";
            $response['tipper'] = "TipoPersona";
            $response['tipemp'] = "NaturalezaJuridicaEmpleador";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['prinom'] = "PrimerNombreEmpleador";
            $response['segnom'] = "SegundoNombreEmpleador";
            $response['priape'] = "PrimerApellidoEmpleador";
            $response['segape'] = "SegundoApellidoEmpleador";
            $response['fecsol'] = "FechaSolicitud";
            $response['perafigra'] = "PerdidaAfiliacionCausaGrave";
            $response['fecafi'] = "FechaEfectivaAfiliacion";
            $response['razsoc'] = "RazonSocial";
            $response['matmer'] = "NumeroMatriculaMercantil";
            $response['coddep'] = "Departamento";
            $response['codmun'] = "Municipio";
            $response['direccion'] = "DireccionContacto";
            $response['telefono'] = "NumeroTelefono";
            $response['email'] = "CorreoElectronico";
            $response['tipdocrep'] = "TipoDocumentoRepresentante";
            $response['numdocrep'] = "NumeroDocumentoRepresentante";
            $response['prinom2'] = "PrimerNombreRepresentante";
            $response['segnom2'] = "SegundoNombreRepresentante";
            $response['priape2'] = "PrimerApellidoRepresentante";
            $response['segape2'] = "SegundoApellidoRepresentante";
            $response['codcaj'] = "CodigoCajaCompensacionFamiliarAnterior";
            $response['pazsal'] = "PazSalvo";
            $response['fecpazsal'] = "FechaPazYSalvo";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
            $response['siafissfant'] = "Manifestacion";
        }
        //desafiliacion
        if ($tabla == "sat06") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['fecsol'] = "FechaSolicitud";
            $response['fecdes'] = "FechaEfectivaDesafiliacion";
            $response['coddep'] = "Departamento";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
            $response['pazsal'] = "PazSalvo";
            $response['fecpazsal'] = "FechaPazYSalvo";
        }
        // perdida grave
        if ($tabla == "sat08") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['tipper'] = "TipoPersona";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['fecper'] = "FechaPerdidaAfiliacion";
            $response['razsoc'] = "RazonSocial";
            $response['causa'] = "CausalRetiro";
            $response['coddep'] = "Departamento";
            $response['priape'] = "PrimerApellidoEmpleador";
            $response['prinom'] = "PrimerNombreEmpleador";
            $response['estado'] = "EstadoReporte";
        }
        //inicio relacion laboral
        if ($tabla == "sat09") {
            $response['tipini'] = "TipoInicio";
            $response['fecini'] = "FechaInicio";
            $response['segnom'] = "SegundoNombre";
            $response['segape'] = "SegundoApellido";
            $response['sexo'] = "Sexo";
            $response['fecnac'] = "FechaNacimiento";
            $response['coddep'] = "Departamento";
            $response['codmun'] = "Municipio";
            $response['direccion'] = "Direccion";
            $response['telefono'] = "Telefono";
            $response['email'] = "Correo";
            $response['salario'] = "Salario";
            $response['tipsal'] = "TipoSalario";
            $response['hortra'] = "HorasTrabajo";
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['tipdoctra'] = "TipoDocumentoTrabajador";
            $response['numdoctra'] = "NumeroDocumentoTrabajador";
            $response['prinom'] = "PrimerNombre";
            $response['priape'] = "PrimerApellido";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
        }
        // termino relacion laboral
        if ($tabla == "sat10") {
            $response['tipter'] = "TipoTerminacion";
            $response['fecter'] = "FechaTerminacion";
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['tipdoctra'] = "TipoDocumentoTrabajador";
            $response['numdoctra'] = "NumeroDocumentoTrabajador";
            $response['prinom'] = "PrimerNombre";
            $response['priape'] = "PrimerApellido";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
        }
        //licencias
        if ($tabla == "sat12") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['tiplin'] = " ";
            $response['fecini'] = " ";
            $response['fecfin'] = " ";
            $response['tipdoctra'] = "TipoDocumentoTrabajador";
            $response['numdoctra'] = "NumeroDocumentoTrabajador";
            $response['priape'] = "PrimerApellidoTrabajador";
            $response['prinom'] = "PrimerNombreTrabajador";
            $response['indnov'] = " ";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
        }
        //modificacion salario
        if ($tabla == "sat13") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['numtrasat'] = "NumeroTransaccion";
            $response['tipper'] = "TipoPersona";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['fecmod'] = "FechaModificacionSalario";
            $response['tipdoctra'] = "TipoDocumentoTrabajador";
            $response['numdoctra'] = "NumeroDocumentoTrabajador";
            $response['prinom'] = "PrimerNombreTrabajador";
            $response['priape'] = "PrimerApellidoTrabajador";
            $response['salario'] = "Salario";
            $response['tipsal'] = "TipoSalario";
            $response['autmandat'] = "AutorizacionManejoDatos";
            $response['autenvnot'] = "AutorizacionNotificaciones";
        }
        //estado pago aportes
        if ($tabla == "sat15") {
            $response['numtraccf'] = "NumeroRadicadoSolicitud";
            $response['tipdocemp'] = "TipoDocumentoEmpleador";
            $response['numdocemp'] = "NumeroDocumentoEmpleador";
            $response['sersat'] = "SerialSAT";
            $response['estpag'] = "EstadoPago";
        }
        return $response;
    }

    public function tokenSat($path, $nit, $pass, $grant_type, $client_id)
    {
        $ch = curl_init();
        $headers  = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $postData = array(
            'username' => $nit,
            'password' => $pass,
            'grant_type' => $grant_type,
            'client_id' => $client_id
        );
        $data = http_build_query($postData);
        curl_setopt($ch, CURLOPT_URL, $path . "token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result     = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($result, true);
        //Debug::addVariable("a",print_r($result,true));
        //Debug::addVariable("b",$statusCode);
        //throw new DebugException(0);
        return $response['token_type'] . " " . $response['access_token'];
    }

    public function convertirModeloTabla($nombre_tabla = '')
    {
        if (!empty($nombre_tabla)) {
            $modelo = "";
            $array_tabla = explode("_", $nombre_tabla);
            foreach ($array_tabla as $parte_tabla) {
                $modelo .= ucfirst($parte_tabla);
            }
            return $modelo;
        } else {
            return false;
        }
    }
}
