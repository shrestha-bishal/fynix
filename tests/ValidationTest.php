<?php
declare(strict_types=1);

namespace Fynix\Tests;

use Fynix\Contracts\ValidationListener;
use Fynix\Exceptions\UndeclaredPropertyException;
use Fynix\Exceptions\UnknownClassException;
use Fynix\Rule;
use Fynix\RuleSet;
use Fynix\Rules\AllOf;
use Fynix\Rules\AnyOf;
use Fynix\Rules\Not;
use Fynix\ValidationHandler;
use Fynix\ValidationRegistry;
use Fynix\Validators\EmailValidator;
use Fynix\Validators\ImageValidator;
use Fynix\Validators\ImagesValidator;
use Fynix\Validators\NumberValidator;
use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Validators\PasswordValidator;
use Fynix\Validators\PhoneNumberValidator;
use Fynix\Validators\StringValidator;
use Fynix\Validators\UsernameValidator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        ValidationRegistry::clear();
        ValidationHandler::clearListeners();
    }

    protected function tearDown(): void
    {
        ValidationHandler::clearListeners();
    }

    public function testRuleBuildsEveryConcreteValidatorWithDerivedLabels(): void
    {
        $cases = [
            'string' => ['firstName', 'First Name', StringValidator::class],
            'number' => ['age', 'Age', NumberValidator::class],
            'email' => ['email', 'Email', EmailValidator::class],
            'phoneNumber' => ['phoneNumber', 'Phone Number', PhoneNumberValidator::class],
            'password' => ['password', 'Password', PasswordValidator::class],
            'image' => ['profileImage', 'Profile Image', ImageValidator::class],
            'images' => ['galleryImages', 'Gallery Images', ImagesValidator::class],
            'object' => ['address', 'Address', ObjectValidator::class],
            'objectArray' => ['items', 'Items', ObjectArrayValidator::class],
            'username' => ['userName', 'User Name', UsernameValidator::class],
        ];

        foreach ($cases as $method => [$field, $label, $className]) {
            $validator = in_array($method, ['object', 'objectArray'], true)
                ? Rule::$method($field, Address::class)
                : Rule::$method($field);

            self::assertInstanceOf($className, $validator);
            self::assertSame($label, $validator->name());
        }
    }

    public function testAdditionalValidatorsAreAvailableAndValidateValues(): void
    {
        self::assertNull(Rule::boolean('enabled')->validate(true));
        self::assertNotNull(Rule::boolean('enabled')->validate('true'));
        self::assertNull(Rule::dateTime('createdAt')->validate('2026-09-11'));
        self::assertNull(Rule::arrayOf('tags')->min(1)->validate(['php']));
        self::assertNull(Rule::url('website')->validate('https://example.com'));
        self::assertNull(Rule::uuid('id')->validate('550e8400-e29b-41d4-a716-446655440000'));
        self::assertNull(Rule::integer('count')->min(1)->validate(2));
        self::assertNull(Rule::decimal('price')->max(10.5)->validate(9.5));
        self::assertNull(Rule::enum('state', Status::class)->validate(Status::Active));
        self::assertNull(Rule::enum('state', Status::class)->validate('active'));
        self::assertNull(Rule::ipAddress('ip')->validate('127.0.0.1'));
        self::assertNull(Rule::regex('code', '/^[A-Z]+$/')->validate('ABC'));
    }

    public function testLabelDerivationSupportsSnakeCaseAcronymsAndEmptyOverride(): void
    {
        self::assertSame('First Name', Rule::string('first_name')->name());
        self::assertSame('Api URL', Rule::string('apiURL')->name());
        self::assertSame('', Rule::string('firstName')->label('')->name());
    }

    public function testScopedRuleChecksOwnerProperties(): void
    {
        $validator = Rule::on(User::class)->string('firstName');

        self::assertSame('firstName', $validator->propertyName());
        self::assertSame('First Name', $validator->name());
    }

    public function testScopedRuleThrowsTypedDefinitionExceptions(): void
    {
        try {
            Rule::on('NotARealClass');
            self::fail('Expected an unknown class exception.');
        } catch (UnknownClassException $exception) {
            self::assertSame('Class or interface NotARealClass does not exist.', $exception->getMessage());
        }

        try {
            Rule::on(User::class)->string('doesNotExist');
            self::fail('Expected an undeclared property exception.');
        } catch (UndeclaredPropertyException $exception) {
            self::assertSame(User::class . ' does not contain property doesNotExist.', $exception->getMessage());
        }
    }

    public function testScopedObjectRulesCheckBothPropertyAndTargetClass(): void
    {
        self::expectException(UndeclaredPropertyException::class);
        Rule::on(User::class)->object('doesNotExist', Address::class);
    }

    public function testBareObjectRulesCheckOnlyTargetClass(): void
    {
        self::expectException(UnknownClassException::class);
        Rule::object('address', 'NotARealClass');
    }

    public function testRuleSetDelegatesAndRejectsUnknownMethods(): void
    {
        $ruleSet = new RuleSet(User::class);
        self::assertInstanceOf(StringValidator::class, $ruleSet->string('firstName'));

        self::expectException(\BadMethodCallException::class);
        $ruleSet->missing('firstName');
    }

    public function testCompletenessMapsEveryConcreteValidatorToRuleAndRuleSet(): void
    {
        foreach (glob(__DIR__ . '/../src/Validators/*Validator.php') ?: [] as $file) {
            $reflection = new ReflectionClass('Fynix\\Validators\\' . basename($file, '.php'));
            if ($reflection->isAbstract() || $reflection->isInterface()) {
                continue;
            }

            $method = lcfirst(substr($reflection->getShortName(), 0, -strlen('Validator')));
            self::assertTrue(method_exists(Rule::class, $method));
            self::assertTrue(method_exists(RuleSet::class, $method));
        }
    }

    public function testConcreteValidatorConstructorsAreNotPublic(): void
    {
        foreach (glob(__DIR__ . '/../src/Validators/*Validator.php') ?: [] as $file) {
            $reflection = new ReflectionClass('Fynix\\Validators\\' . basename($file, '.php'));
            if ($reflection->isAbstract() || $reflection->isInterface()) {
                continue;
            }

            self::assertNotNull($reflection->getConstructor());
            self::assertFalse($reflection->getConstructor()?->isPublic());
            self::assertTrue($reflection->hasMethod('__makeInternal'));
        }
    }

    public function testFluentMethodsReturnNewInstances(): void
    {
        $cases = [
            [Rule::string('name'), 'min', [3]],
            [Rule::string('name'), 'max', [10]],
            [Rule::string('name'), 'length', [2, 10]],
            [Rule::string('name'), 'optional', []],
            [Rule::number('age'), 'min', [18]],
            [Rule::number('age'), 'max', [99]],
            [Rule::email('email'), 'verifyDomain', [false]],
            [Rule::image('image'), 'maxFileSizeMB', [2]],
            [Rule::images('images'), 'min', [1]],
            [Rule::images('images'), 'max', [2]],
            [Rule::objectArray('items', Item::class), 'min', [1]],
            [Rule::objectArray('items', Item::class), 'max', [2]],
            [Rule::username('username'), 'uniqueUsing', [static fn(string $value): bool => false]],
        ];

        foreach ($cases as [$validator, $method, $arguments]) {
            self::assertNotSame($validator, $validator->{$method}(...$arguments));
        }
    }

    public function testExistingValidationBehaviorRemainsAvailableThroughRule(): void
    {
        $validator = Rule::string('name')->min(3)->max(10)->optional();

        self::assertSame(3, $validator->minLength());
        self::assertSame(10, $validator->maxLength());
        self::assertFalse($validator->requiredState());
        self::assertNull($validator->validateField(null));
        self::assertNotNull($validator->validateField('ab'));
        self::assertNull($validator->validateField('Bishal'));
    }

    public function testCombinatorsShortCircuitAndInvert(): void
    {
        $all = new AllOf([Rule::string('name')->min(3), Rule::string('name')->max(10)]);
        self::assertNotNull($all->validate('x'));
        self::assertNull($all->validate('valid'));

        $any = new AnyOf([Rule::string('name')->min(10), Rule::string('name')->max(10)]);
        self::assertNull($any->validate('valid'));
        self::assertNotNull($any->validate('x'));

        $not = new Not(Rule::string('name')->min(3), 'Name must not be a long string.');
        self::assertNull($not->validate('x'));
        self::assertSame('Name must not be a long string.', $not->validate('valid')?->message);
    }

    public function testNestedObjectsAndArraysUseRuleSetRecursively(): void
    {
        ValidationRegistry::register(Address::class, static fn(RuleSet $rules): array => [
            $rules->string('street'),
        ]);
        ValidationRegistry::register(Item::class, static fn(RuleSet $rules): array => [
            $rules->string('name'),
        ]);
        ValidationRegistry::register(Order::class, static fn(RuleSet $rules): array => [
            $rules->object('address', Address::class),
            $rules->objectArray('items', Item::class),
        ]);

        $order = new Order();
        $order->address = new Address();
        $order->items = [new Item()];

        $errors = ValidationHandler::validateAndFlatten($order);
        self::assertArrayHasKey('address.street', $errors);
        self::assertArrayHasKey('items.0.name', $errors);
    }

    public function testListenersFireOnceForEachPublicValidateCall(): void
    {
        $listener = new TestListener();
        ValidationHandler::addListener($listener);
        ValidationRegistry::register(User::class, static fn(RuleSet $rules): array => [
            $rules->string('firstName'),
        ]);

        ValidationHandler::validate(new User());

        self::assertSame(1, $listener->beforeCount);
        self::assertSame(1, $listener->afterCount);
    }
}

final class TestListener implements ValidationListener
{
    public int $beforeCount = 0;
    public int $afterCount = 0;

    public function beforeValidate(object $instance): void
    {
        $this->beforeCount++;
    }

    public function afterValidate(object $instance, array $errors): void
    {
        $this->afterCount++;
    }
}

final class User
{
    public string $firstName = '';
}

final class Address
{
    public string $street = '';
}

final class Item
{
    public string $name = '';
}

final class Order
{
    public ?Address $address = null;

    /** @var list<Item> */
    public array $items = [];
}

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
