<?php
declare(strict_types=1);

namespace Fynix\Contracts;

interface ValidationListener
{
    public function beforeValidate(object $instance): void;

    /** @param array<string|int, mixed> $errors */
    public function afterValidate(object $instance, array $errors): void;
}
