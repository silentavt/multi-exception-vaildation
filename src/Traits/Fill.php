<?php


namespace Silentavt\MultiExceptionValidation\Traits;


use Silentavt\MultiExceptionValidation\Exceptions\MultiException;
use Silentavt\MultiExceptionValidation\Exceptions\ValidationErrorException;

trait Fill
{
    public function fill($dataArray)
    {
        $validationErrors = new MultiException();
        foreach ($dataArray as $key => $value) {
            if (property_exists($this, $key)) {
                $validationRules = $this->getValidationRules();
                if (array_key_exists($key, $validationRules) && is_array($validationRules[$key])) {
                    foreach ($validationRules[$key] as $check) {
                        try {
                            $validatorClassName = $this->getValidatorClassName();
                            $validatorClassName::$check($key, $value);
                        } catch (ValidationErrorException $e) {
                            $validationErrors->add($e);
                        }
                    }
                }
                $this->$key = filter_var($value, FILTER_SANITIZE_STRING);
            }
        }
        if (count($validationErrors) > 0) {
            throw $validationErrors;
        }
    }
    
    abstract protected function getValidatorClassName();
    abstract protected function getValidationRules();
}