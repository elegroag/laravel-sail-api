<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class AuthException extends Exception
{

    public function __construct(string $message = '', int $code = 0, $extra = null, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->extra = $extra;
    }

    public $extra;

    public static $errors = [];

    protected $orderId;

    public function context()
    {
        return ['order_id' => $this->orderId];
    }

    public function report() {}

    public function render(Request $request)
    {
        return response()->json(
            [
                'success' => false,
                'exception' => 1,
                'message' => $this->getMessage(),
                'request' => $request,
                'out' => [
                    'code' => $this->getCode(),
                    'file' => basename($this->getFile()),
                    'line' => $this->getLine(),
                ],
            ],
            205
        );
    }
}
