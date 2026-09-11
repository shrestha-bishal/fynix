<?php
declare(strict_types=1);

namespace Fynix\Rules;

use Fynix\Contracts\Validatable;
use Fynix\ValidationError;
use Fynix\Validators\ValidatorBase;

final class AllOf implements Validatable
{
    /** @param array<Validatable> $rules */
    public function __construct(private array $rules)
    {
    }

    public function validate(mixed $value): ?ValidationError
    {
        foreach ($this->rules as $rule) {
            $error = $rule instanceof ValidatorBase
                ? $rule->validateField($value)
                : $rule->validate($value);
            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }
}
