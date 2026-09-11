<?php
namespace Fynix;

use Fynix\Validators\ValidatorBase;

class ValidationError {
    public ?ValidatorBase $rule;
    public string $message;
    public string $field_name;
    public string $code;
    /** @var array<string, mixed> */
    public array $parameters;

    /** @param array<string, mixed> $parameters */
    public function __construct(
        ?ValidatorBase $rule,
        string $message,
        string $code = 'validation.invalid',
        array $parameters = [],
        ?string $fieldName = null
    )
    {
        $this->rule = $rule;
        $this->message = $message;
        $this->field_name = $fieldName ?? $rule?->propertyName() ?? '';
        $this->code = $code;
        $this->parameters = $parameters;
    }

    /** @param array<string, mixed> $parameters */
    public static function forField(
        string $fieldName,
        string $message,
        string $code = 'validation.invalid',
        array $parameters = []
    ): self {
        return new self(null, $message, $code, $parameters, $fieldName);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'field' => $this->field_name,
            'code' => $this->code,
            'message' => $this->message,
            'parameters' => $this->parameters,
        ];
    }
}