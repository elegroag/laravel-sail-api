<?php 

namespace App\Http\Controllers\Adapter;

use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public $request;
    private $models = array();
    
    public function __construct()
    {
        $this->request = request();
    }
    
    public function getPostParam($name)
    {
        return $this->request->input($name);
    }

    public function __get(string $name='')
    {
        if (class_exists($name, false)) {
            if (!array_key_exists($name, $this->models)) {
                $this->models[$name] = new $name();
            }
            return $this->models[$name];
        }
        return false;
    }

    public function setResponse($type){
		switch($type){
			case 'ajax':
                header('Content-Type: application/json; charset=utf-8');
                break;
			case 'view':
                header('Content-Type: text/html; charset=utf-8');
				break;
			case 'xml':
                header('Content-Type: application/xml; charset=utf-8');
				break;
			case 'json':
                header('Content-Type: application/json; charset=utf-8');
				break;
            case 'empty':
                header('Content-Type: text/plain; charset=utf-8');
                break;
		}
		return null;
	}

    public function renderObject($object, bool|null $format = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!$format) {
            echo json_encode($object);
        } else {
            echo json_encode($object, JSON_NUMERIC_CHECK);
        }
    }

    protected function renderFile(string $filepath='')
    {
        $ext = substr(strrchr($filepath, "."), 1);
        $mimes = self::mimeType();
        $mime = (isset($mimes["{$ext}"])) ? $mimes["{$ext}"] : 'application/*.*';
        header("Content-Type: {$mime}; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . basename($filepath) . "");
        header('Cache-Control: must-revalidate');
        header('Expires: 0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        readfile($filepath);
        exit(); 
    }

    public static function mimeType()
    {
        return array(
            'csv'    =>  'application/x-csv',
            'pdf'    =>  'application/pdf',
            'gz'    =>   'application/x-gzip',
            'tar'    =>  'application/x-tar',
            'zip'    =>  'application/zip',
            'rar'    =>  'application/x-rar',
            'gif'    =>  'image/gif',
            'jpeg'    => 'image/jpeg',
            'jpg'    =>  'image/jpeg',
            'jpe'    =>  'image/jpeg',
            'png'    =>  'image/png',
            'tiff'    => 'image/tiff',
            'tif'    =>  'image/tiff',
            'txt'    =>  'text/plain',
            'text'    => 'text/plain',
            'log'    =>  'text/x-log',
            'xml'    =>  'application/xml',
            'xsl'    =>  'application/xml',
            'doc'    =>  'application/vnd.ms-office',
            'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'    =>  'application/vnd.ms-excel',
            'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'word'    => 'application/msword',
            '7z'    =>   'application/x-7z-compressed',
            'jar'    =>  'application/x-java-application'
        );
    }

}