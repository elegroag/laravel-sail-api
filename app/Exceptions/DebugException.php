<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DebugException extends Exception
{

    protected array|string|null $errors = null;
    protected int $orderId;

    public function __construct(
        string $message = '',
        int $code = 0,
        array|string|null $errors = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
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

    public function getErrors(?Request $request = null)
    {
        $data = [
            'success' => false,
            'flag' => false,
            'exception' => 1,
            'message' => $this->getMessage(),
            'msj' => $this->getMessage(),
            'errors' => is_array($this->errors) || is_object($this->errors) ?  $this->errors : json_decode($this->errors)
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
        return response()->json($this->getErrors($request), 203);
    }

    public function add(string $key, array $collect)
    {
        $this->errors[$key] = $collect;
    }

    public function item(string $key)
    {
        return $this->errors[$key] ?? '';
    }

    public function addMessage($item, $value) {}
}
