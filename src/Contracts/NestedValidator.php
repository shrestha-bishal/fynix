<?php
declare(strict_types=1);

namespace Fynix\Contracts;

interface NestedValidator extends Validatable
{
    public function propertyName(): string;

    public function requiredState(): bool;

    public function targetClass(): string;

    public function isCollection(): bool;

    public function accepts(mixed $value): bool;

    public function acceptsItem(mixed $value): bool;

    public function minItems(): ?int;

    public function maxItems(): ?int;
}
