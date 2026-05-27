<?php

namespace App\Http\Controllers\Adapter;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class ApplicationController extends Controller
{
    private array $models = [];

    public function __get(string $name = '')
    {
        if (class_exists($name, false)) {
            if (! array_key_exists($name, $this->models)) {
                $this->models[$name] = new $name;
            }

            return $this->models[$name];
        }

        return false;
    }

    public function setResponse(string $type)
    {
        switch ($type) {
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

    public function renderObject(array|object $object)
    {
        return response()->json($object, 203, [
            'Content-Type' => 'application/json',
            'Charset' => 'utf-8'
        ]);
    }

    protected function renderFile(string $filepath = '')
    {
        $ext = substr(strrchr($filepath, '.'), 1);
        $mimes = self::mimeType();
        $mime = (isset($mimes["{$ext}"])) ? $mimes["{$ext}"] : 'application/*.*';
        header("Content-Type: {$mime}; charset=utf-8");
        header('Content-Disposition: attachment; filename=' . basename($filepath) . '');
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
        return [
            'csv' => 'application/x-csv',
            'pdf' => 'application/pdf',
            'gz' => 'application/x-gzip',
            'tar' => 'application/x-tar',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'log' => 'text/x-log',
            'xml' => 'application/xml',
            'xsl' => 'application/xml',
            'doc' => 'application/vnd.ms-office',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'word' => 'application/msword',
            '7z' => 'application/x-7z-compressed',
            'jar' => 'application/x-java-application',
        ];
    }

    /**
     * Retorna el usuario activo desde la sesión.
     * Equivalente migrado de getActUser usado en controladores Kumbia.
     * Si se pasa $key, retorna ese campo dentro de session('user').
     */
    public static function getActUser(?string $key = null)
    {
        if (! session()->has('user')) {
            return null;
        }
        $user = session('user');
        if ($key === null) {
            return $user;
        }

        return $user[$key] ?? null;
    }

    public function renderText(?string $html = null)
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    /**
     * Clean input data
     *
     * @param  string|null  $input
     * @return string
     */
    protected function cleanInput($input)
    {
        if ($input === null) {
            return '';
        }
        if (is_string($input)) {
            $input = addslashes($input);
            $input = trim($input);
            $input = strip_tags($input);
        }

        return $input;
    }

    protected function clp(Request $request, string $name, $default = '')
    {
        return $this->cleanInput($request->input($name, $default));
    }

    public function setParamToView($name, $value) {}

    public function errorFunc(string $msg, ?int $code = null)
    {
        set_flashdata('error', [
            'msj' => $msg,
            'code' => $code,
        ]);

        return ['flag' => false, 'msg' => $msg];
    }

    public function successFunc(string $msg, ?string $template = null)
    {
        set_flashdata('success', [
            'msj' => $msg,
            'template' => $template,
        ]);

        return ['flag' => true, 'msg' => $msg];
    }

    public function setLogger(string $msg)
    {
        Log::stack(['single', 'slack'])->debug($msg);
    }

    protected function getDebug(Throwable $e)
    {
        if ($e instanceof DebugException) {
            $debug = $e;
        } elseif ($e instanceof QueryException || $e instanceof PDOException) {
            $debug = new DebugException('Error de base de datos (SQL)', 500, $e->getMessage());
        } elseif ($e instanceof AuthException) {
            $debug = new DebugException('Error de autenticación', 501, $e->getMessage());
        } else {
            $debug = new DebugException('Error de sintaxis del sistema', 501, [
                $e->getMessage(),
                $e->getLine(),
                basename($e->getFile()),
                $e->getTraceAsString()
            ]);
        }
        return $debug;
    }

    protected function handleException(\Throwable $e, ?Request $request = null): JsonResponse
    {
        $debug = $this->getDebug($e);
        return $debug->render($request);
    }

    protected function captureException(\Throwable $e, ?Request $request = null): array
    {
        $debug = $this->getDebug($e);
        return $debug->getErrors($request);
    }
}
