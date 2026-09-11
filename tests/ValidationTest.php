<?php

namespace Fynix\Tests;

use Fynix\ValidationHandler;
use Fynix\ValidationError;
use Fynix\ValidationRegistry;
use Fynix\Rule;
use Fynix\Rules;
use Fynix\Validators\EmailValidator;
use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Validators\NumberValidator;
use Fynix\Validators\PasswordValidator;
use Fynix\Validators\StringValidator;
use Fynix\Validators\UsernameValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Fynix\nameof;

final class ValidationTest extends TestCase
{
    protected function tearDown(): void
    {
        ValidationRegistry::clearCache();
    }

    public function testStringDefaultsAndFluentConfiguration(): void
    {
        $validator = (new StringValidator('Name', 'name'))->min(3)->max(10)->isRequired(false);

        self::assertSame(3, $validator->minLength());
        self::assertSame(10, $validator->maxLength());
        self::assertFalse($validator->requiredState());
        self::assertNull($validator->validateField(null));
        self::assertNotNull($validator->validateField('ab'));
        self::assertNull($validator->validateField('Bishal'));
    }

    public function testNameofReturnsExistingPropertyNames(): void
    {
        self::assertSame('name', nameof(Node::class, 'name'));
        self::assertSame('name', nameof(new Node(), 'name'));
    }

    public function testNameofRejectsUnknownProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);

        nameof(Node::class, 'missing');
    }

    public function testRuleStringMatchesStringValidatorFactory(): void
    {
        $ruleValidator = Rule::string('firstName');
        $directValidator = StringValidator::make('firstName');

        self::assertSame($directValidator::class, $ruleValidator::class);
        self::assertSame('First Name', $ruleValidator->name());
        self::assertSame($directValidator->name(), $ruleValidator->name());
        self::assertSame($directValidator->propertyName(), $ruleValidator->propertyName());
        self::assertSame(
            $directValidator->validateField('x')?->code,
            $ruleValidator->validateField('x')?->code
        );
    }

    public function testRuleStringCanValidateAClassProperty(): void
    {
        $validator = Rule::string(User::class, 'firstName');

        self::assertSame('firstName', $validator->propertyName());
    }

    public function testRuleStringRejectsAnUnknownClassProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(User::class . ' does not contain property doesNotExist.');

        Rule::string(User::class, 'doesNotExist');
    }

    public function testRuleStringRejectsAnUnknownClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class or interface NotARealClass does not exist.');

        Rule::string('NotARealClass', 'x');
    }

    public function testRuleHasAnEntryPointForEveryConcreteValidator(): void
    {
        foreach (glob(__DIR__ . '/../src/Validators/*Validator.php') as $validatorFile) {
            $className = 'Fynix\\Validators\\' . basename($validatorFile, '.php');
            $reflection = new \ReflectionClass($className);

            if ($reflection->isAbstract()) {
                continue;
            }

            $methodName = lcfirst(substr($reflection->getShortName(), 0, -strlen('Validator')));

            self::assertTrue(
                method_exists(Rule::class, $methodName),
                "Missing Rule::$methodName() for {$reflection->getName()}."
            );
        }
    }

    public function testFluentRulesBuilderCreatesRegistryRules(): void
    {
        $resolver = Rules::for(Node::class)
            ->string(nameof(Node::class, 'name'))
            ->min(2)
            ->max(20);

        ValidationRegistry::register(Node::class, $resolver);

        $node = new Node();
        $node->name = 'Root';

        self::assertSame([], ValidationHandler::validate($node));
    }

    public function testNumberMinAndMaxAreNumericConstraints(): void
    {
        $validator = (new NumberValidator('Age', 'age'))->min(18)->max(99);

        self::assertNotNull($validator->validateField(17));
        self::assertNull($validator->validateField(25));
        self::assertNotNull($validator->validateField(100));
        self::assertNotNull($validator->validateField('not-a-number'));
    }

    public function testPasswordCanReturnAllApplicableErrors(): void
    {
        $errors = (new PasswordValidator('Password', 'password'))->validateFieldAll('abc');
        $codes = array_map(static fn($error): string => $error->code, $errors);

        self::assertContains('password.uppercase', $codes);
        self::assertContains('password.number', $codes);
        self::assertContains('password.special', $codes);
        self::assertCount(4, $errors);
    }

    public function testEmailDoesNotRequireNetworkDnsByDefault(): void
    {
        $validator = new EmailValidator('Email', 'email');
        $error = $validator->validateField('user@example.test');

        self::assertNull($error);
        self::assertSame('email.invalid', (new EmailValidator('Email', 'email'))
            ->validateField('invalid-email')?->code);
    }

    public function testUsernameCanUseAnApplicationProvidedUniquenessChecker(): void
    {
        $validator = (new UsernameValidator('Username', 'username'))
            ->uniqueUsing(static fn(string $username): bool => $username === 'taken_user');

        self::assertNull($validator->validateField('available_user'));
        self::assertSame('username.taken', $validator->validateField('taken_user')?->code);
        self::assertSame('username.characters', $validator->validateField('bad-name')?->code);
    }

    public function testStructuredErrorContainsCodeAndParameters(): void
    {
        $error = (new StringValidator('Name', 'name'))->validateField('<b>Name</b>');

        self::assertNotNull($error);
        self::assertSame('html.forbidden', $error->code);
        self::assertSame('name', $error->toArray()['field']);
    }

    public function testMissingRequiredNestedObjectIsReported(): void
    {
        ValidationRegistry::register(Profile::class, static fn(Profile $profile): array => [
            new ObjectValidator('address', Address::class),
        ]);

        $errors = ValidationHandler::validate(new Profile());

        self::assertSame('address is required.', $errors['address']);

        $structuredErrors = ValidationHandler::validate(new Profile(), false);
        self::assertInstanceOf(ValidationError::class, $structuredErrors['address']);
        self::assertSame('required', $structuredErrors['address']->code);
    }

    public function testCyclicObjectsDoNotRecurseForever(): void
    {
        ValidationRegistry::register(Node::class, static fn(Node $node): array => [
            (new StringValidator('Name', 'name'))->length(2, 20),
            (new ObjectValidator('child', Node::class))->optional(),
        ]);

        $node = new Node();
        $node->name = 'Root';
        $node->child = $node;

        self::assertSame([], ValidationHandler::validate($node));
    }

    public function testObjectArrayValidatesCardinalityAndItems(): void
    {
        ValidationRegistry::register(Item::class, static fn(Item $item): array => [
            new StringValidator('Name', 'name'),
        ]);
        ValidationRegistry::register(Order::class, static fn(Order $order): array => [
            (new ObjectArrayValidator('items', Item::class))->min(1)->max(2),
        ]);

        $order = new Order();
        $order->items = [new Item(), new Item(), new Item()];
        $errors = ValidationHandler::validate($order);

        self::assertSame('items can contain at most 2 items.', $errors['items']);
    }
}

final class Profile
{
    public ?Address $address = null;
}

final class Address
{
}

final class Node
{
    public string $name = '';
    public ?Node $child = null;
}

final class User
{
    public string $firstName = '';
}

final class Item
{
    public string $name = '';
}

final class Order
{
    /** @var list<Item> */
    public array $items = [];
}