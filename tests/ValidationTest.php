<?php

namespace Fynix\Tests;

use Fynix\ValidationHandler;
use Fynix\ValidationError;
use Fynix\ValidationRegistry;
use Fynix\Validators\EmailValidator;
use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Validators\NumberValidator;
use Fynix\Validators\PasswordValidator;
use Fynix\Validators\StringValidator;
use PHPUnit\Framework\TestCase;

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

final class Item
{
    public string $name = '';
}

final class Order
{
    /** @var list<Item> */
    public array $items = [];
}