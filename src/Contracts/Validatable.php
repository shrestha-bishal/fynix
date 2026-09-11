<?php
declare(strict_types=1);

namespace Fynix\Contracts;

use Fynix\ValidationError;

interface Validatable
{
    public function validate(mixed $value): ?ValidationError;
}
