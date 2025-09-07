<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio37;

class GuardarArchivoService
{
    private $tipopc;
    private $coddoc;
    private $id;
    private $db;

    public function __construct($argv)
    {
        $this->tipopc = $argv['tipopc'];
        $this->coddoc = $argv['coddoc'];
        $this->id = $argv['id'];
        $this->db = DbBase::rawConnect();
    }

    public function main()
    {
        $time = strtotime('now');
        if (is_null($_FILES)) {
            throw new DebugException("Error no hay archivos disponibles en servidor", 301);
        }
        $item = 'archivo_' . $this->id . '_' . $this->coddoc;
        if (!isset($_FILES[$item])) {
            throw new DebugException("Error no es valido el name del activo", 301);
        }

        $extension = explode(".", $_FILES[$item]['name']);
        $name = $this->tipopc . "_" . $this->id . "_{$this->coddoc}_{$time}." . end($extension);
        $_FILES[$item]['name'] = $name;

        $estado = $this->uploadFile($item, 'temp/');
        if ($estado != false) {
            $mercurio37 = $this->salvarDatos($name);
        } else {
            throw new DebugException("No se cargo el tamaño del archivo muy grande o no es valido", 301);
        }
        return $mercurio37;
    }


    public function salvarDatos($params)
    {
        if (is_array($params)) {
            $name = $params['file'];
            $fhash = $params['fhash'];
        } else {
            $name = $params;
            $fhash = null;
        }
        $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $this->id)
            ->where('coddoc', $this->coddoc)
            ->first();

        if ($mercurio37) {
            $mercurio37->setArchivo($name);
            $mercurio37->setFhash($fhash);
        } else {
            $mercurio37 = new Mercurio37();
            $mercurio37->setTipopc($this->tipopc);
            $mercurio37->setNumero($this->id);
            $mercurio37->setCoddoc($this->coddoc);
            $mercurio37->setArchivo($name);
            $mercurio37->setFhash($fhash);
        }

        $mercurio37->save();

        $mercurio10 = Mercurio10::where('numero', $this->id)
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        if ($mercurio10) {
            //los campos devueltos se borran
            if ($mercurio10->estado == 'D') {
                $corregir = explode(";", $mercurio10->campos_corregir);
                $nuevos = array_filter($corregir, function ($row) {
                    return intval($row) != intval($this->coddoc);
                });
                $campos_corregir = (count($nuevos) > 0) ? implode(';', $nuevos) : "";
                $mercurio10->campos_corregir = $campos_corregir;
                $mercurio10->save();
            }
        }

        return $mercurio37;
    }

    function uploadFile($name, string|null $dir = null)
    {
        if (!isset($_FILES[$name])) {
            return false;
        }

        if ($_FILES[$name]) {
            ob_clean();

            $dir = storage_path($dir ?? 'temp/');
            return move_uploaded_file($_FILES[$name]['tmp_name'], $dir . htmlspecialchars($_FILES[$name]['name']));
        } else {
            return false;
        }
    }
}
