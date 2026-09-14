<?php
declare(strict_types=1);

namespace Fynix\Rules;

use BadMethodCallException;
use Closure;
use Fynix\RuleSet;
use Fynix\Validators\ValidatorBase;
use InvalidArgumentException;

/**
 * @method self string(string $field)
 * @method self boolean(string $field)
 * @method self dateTime(string $field)
 * @method self arrayOf(string $field)
 * @method self array(string $field)
 * @method self url(string $field)
 * @method self uuid(string $field)
 * @method self integer(string $field)
 * @method self decimal(string $field)
 * @method self enum(string $field, string $enumClass)
 * @method self file(string $field)
 * @method self ipAddress(string $field)
 * @method self regex(string $field, string $pattern)
 * @method self number(string $field)
 * @method self email(string $field)
 * @method self phoneNumber(string $field)
 * @method self password(string $field)
 * @method self image(string $field)
 * @method self images(string $field)
 * @method self object(string $field, string $targetClass)
 * @method self objectArray(string $field, string $targetClass)
 * @method self username(string $field)
 * @method self min(int|float $value)
 * @method self max(int|float $value)
 * @method self length(int $min, int $max)
 * @method self required(bool $required = true)
 * @method self optional()
 * @method self when(Closure $condition)
 *
 * Compatibility builder for the v2 Rules::for() API.
 *
 * The builder stores the immutable validator returned by each fluent call and
 * exposes the resulting definitions through rules().
 */
final class RuleBuilder
{
    /** @var list<ValidatorBase> */
    private array $rules = [];

    private ?ValidatorBase $current = null;

    private RuleSet $ruleSet;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->ruleSet = new RuleSet($className);
    }

    /** @return list<ValidatorBase> */
    public function rules(): array
    {
        return $this->rules;
    }

    /** @return list<ValidatorBase> */
    public function __invoke(object $instance): array
    {
        return $this->rules;
    }

    /** @param array<int, mixed> $arguments */
    public function __call(string $method, array $arguments): static
    {
        if (method_exists($this->ruleSet, $method)) {
            $rule = $this->ruleSet->{$method}(...$arguments);
            if (!$rule instanceof ValidatorBase) {
                throw new InvalidArgumentException("Rule method $method did not return a validator.");
            }

            $this->rules[] = $rule;
            $this->current = $rule;
            return $this;
        }

        if ($this->current !== null && method_exists($this->current, $method)) {
            $configured = $this->current->{$method}(...$arguments);
            if (!$configured instanceof ValidatorBase) {
                throw new InvalidArgumentException("Validator method $method did not return a validator.");
            }

            $last = array_key_last($this->rules);
            if (!is_int($last)) {
                throw new InvalidArgumentException('Cannot configure an empty rule set.');
            }

            $this->rules[$last] = $configured;
            $this->current = $configured;
            return $this;
        }

        throw new BadMethodCallException("Unknown rule method $method.");
    }
}