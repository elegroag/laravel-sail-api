<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class DebugException extends Exception
{
    static $errors = array();
    protected $orderId;

    public function context()
    {
        return ['order_id' => $this->orderId];
    }

    public function report()
    {
        $this->addMessage('mensaje', $this->getMessage());
        $this->addMessage('codigo', $this->getCode());
        $this->addMessage('linea', $this->getLine());
        $this->addMessage('archivo', $this->getFile());
    }

    public function render(Request $request)
    {
        return response()->json(
            [
                "success" => false,
                "exception" => 1,
                "message" => $this->getMessage(),
                "msj" => $this->getMessage(),
                'request' => $request,
                'out' => [
                    'code' => $this->getCode(),
                    'file' => basename($this->getFile()),
                    'line' => $this->getLine()
                ]
            ], 203
        );
    }

    public static function add($key,  $collect)
    {
        self::$errors[$key] = $collect;
    }

    public static function item($key)
    {
        return (isset(self::$errors[$key])) ? self::$errors[$key] : "";
    }

    public function addMessage($item, $value){

    }
} 