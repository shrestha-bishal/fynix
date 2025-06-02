<?php
namespace PhpValidationCore;

use PhpValidationCore\ValidatorBase;

class ValidationError {
    public ValidatorBase $rule;
    public string $message;
    public string $field_name;

    public function __construct(ValidatorBase $rule, $message)
    {
        $this->rule = $rule;
        $this->message = $message;
        $this->field_name = $rule->fieldName;
    }
}