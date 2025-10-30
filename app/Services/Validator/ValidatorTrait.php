<?php

namespace App\Services\Validator;

trait ValidatorTrait
{
    protected $validator;

    protected $entity;

    public function create($data)
    {
        $this->entity = [];
        foreach ($this->fillable as $key) {
            $this->entity[$key] = isset($data[$key]) ? $data[$key] : null;
        }
    }

    public function getData()
    {
        return $this->entity;
    }

    public function initValidator()
    {
        $this->validator = new ValidatorEntity;
        $this->validator->setRules($this->getRules());
    }

    public function validate()
    {
        if (! $this->validator) {
            $this->initValidator();
        }

        return $this->validator->validate($this->entity);
    }

    public function getValidationErrors()
    {
        return $this->validator ? $this->validator->getErrors() : [];
    }

    public function getAttrErrors()
    {
        return $this->validator ? $this->validator->getAttrErrors() : [];
    }

    // Este método debe ser implementado por cada modelo
    abstract protected function getRules();
}
