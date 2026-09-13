<?php
declare(strict_types=1);

namespace Fynix\Rules;

use Fynix\Contracts\Validatable;
use Fynix\ValidationError;
use Fynix\Validators\ValidatorBase;

final class Not implements Validatable
{
    public function __construct(
        private Validatable $rule,
        private string $message = 'The value must not satisfy this rule.'
    ) {
    }

    public function validate(mixed $value): ?ValidationError
    {
        $error = $this->rule->validate($value);

        if ($error !== null) {
            return null;
        }

        return ValidationError::forField('', $this->message, 'rule.not');
    }
}
