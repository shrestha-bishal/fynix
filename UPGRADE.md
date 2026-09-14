# Upgrade guide - Fynix

## v3.1 conditional rules

Conditional rules accept either the field/value form or a closure receiving the
object currently being validated:

```php
use Fynix\RuleSet;
use Fynix\ValidationRegistry;

ValidationRegistry::register(
	Order::class,
	static fn (RuleSet $rules): array => [
		$rules->string('shippingBusinessName')
			->optional()
			->requiredIf('shippingMethod', 'business'),

		$rules->string('internationalCode')
			->optional()
			->requiredIf(
				static fn (Order $order): bool =>
					$order->shippingMethod === 'business' && $order->isInternational
			),
	]
);
```

Use `when()` when the whole validator should be conditional. It gates any
validator configuration, including value, length, and required constraints:

```php
$rules->string('companyName')
	->min(10)
	->when(static fn (Order $order): bool => $order->shippingMethod === 'business');
```

The closure form is also available on `requiredUnless()`, `prohibitedIf()`,
`prohibitedUnless()`, `sameAs()`, and `differentFrom()`. For `sameAs()` and
`differentFrom()`, the closure returns the value to compare against. Predicates
run during validation and receive the actual object, not the `RuleSet` used to
define the rules.

## v3.1 RuleBuilder compatibility

The v2 builder is available as a compatibility layer on top of v3:

```php
use Fynix\Rules;

$rules = Rules::for(Order::class)
	->string('shippingMethod')
	->required()
	->rules();
```

New code should prefer `RuleSet` in registry definitions. `RuleBuilder` keeps
legacy fluent registrations working and supports v3 features such as `when()`.

## DTO property visibility

Registered validation reads declared fields from the validated object, including
private and protected properties:

```php
final class Order
{
	public string $shippingMethod = '';
	public ?string $shippingBusinessName = null;
}
```

`nameof()` checks that a property exists, and the validator reads declared
properties through reflection. Getters and magic properties are not invoked
automatically; use a declared property when defining a rule.