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
        $mercurio01 = (new Mercurio01())->findFirst();

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

        $estado = $this->uploadFile($item, $mercurio01->getPath());
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
        $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$this->id}' and coddoc='{$this->coddoc}'");
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

        if (!$mercurio37->save()){
            throw new DebugException("Error al guardar el archivo", 301);
        }

        $mercurio10 = $this->db->fetchOne("SELECT 
            item, 
            estado, 
            campos_corregir 
            FROM mercurio10 
            WHERE numero='{$this->id}' AND 
            tipopc='{$this->tipopc}' 
            ORDER BY item DESC 
            LIMIT 1;
        ");

        if ($mercurio10) {
            //los campos devueltos se borran
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
                $nuevos = array();
                foreach ($corregir as $row) {
                    if (intval($row) != intval($this->coddoc)) {
                        $nuevos[] = $row;
                    }
                }
                $campos_corregir = (count($nuevos) > 0) ? implode(';', $nuevos) : "";
                (new Mercurio10)->updateAll(
                    "campos_corregir='{$campos_corregir}'", 
                    "conditions: numero='{$this->id}' AND tipopc='{$this->tipopc}' AND item='{$mercurio10['item']}'");
            }
        }

        return $mercurio37;
    }

    function uploadFile($name, $dir)
    {
        if (!isset($_FILES[$name])) {
            return false;
        }

        if ($_FILES[$name]) {
            ob_clean();

            $dir = Core::getInitialPath() . '' . $dir;
            return move_uploaded_file($_FILES[$name]['tmp_name'], htmlspecialchars("$dir/{$_FILES[$name]['name']}"));
        } else {
            return false;
        }
    }
}
