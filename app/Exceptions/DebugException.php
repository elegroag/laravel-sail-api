<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DebugException extends Exception
{
    public static $errors = [];

    protected array|string|null $extra = null;

    protected int $orderId;

    public function __construct(
        string $message = '',
        int $code = 0,
        array|string|null $extra = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->extra = $extra;
    }

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

    public function errors(?Request $request = null)
    {
        $data = [
            'success' => false,
            'flag' => false,
            'exception' => 1,
            'message' => $this->getMessage(),
            'msj' => $this->getMessage(),
            'extra' => json_decode($this->extra)
        ];
        if (config('app.debug') == 'local') {
            $data['out'] = [
                'code' => $this->getCode(),
                'file' => basename($this->getFile()),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString(),
            ];
            $data['request'] = $request->all() ?? [];
        }
        return $data;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json($this->errors($request), 203);
    }

    public static function add(string $key, array $collect)
    {
        self::$errors[$key] = $collect;
    }

    public static function item(string $key)
    {
        return (isset(self::$errors[$key])) ? self::$errors[$key] : '';
    }

    public function addMessage($item, $value) {}
}
