<?php

namespace App\Services\Validator;

use Exception;

class EntityException extends Exception
{
    public $errors;

    public function __construct($errors)
    {
        parent::__construct('Error validación de datos', 501);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
