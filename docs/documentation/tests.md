## Controller tests

Ouzo comes with Ouzo\Tests\ControllerTestCase class designed to ease controller testing.
ControllerTestCase allows you to verify that:
* there's a route for a given url
* controllers methods work as expected
* views are rendered without errors

```php
class UsersControllerTest extends ControllerTestCase
{
    /**
     * @test
     */
    public function shouldRenderIndex()
    {
        //when
        $this->get('/users');

        //then
        $this->assertRenders('Users/index');
    }

    /**
     * @test
     */
    public function shouldRedirectToIndexOnSuccessInCreate()
    {
        //when
        $this->post('/users', array('user' => array(
            'login' => 'login'
        )));

        //then
        $this->assertRedirectsTo(usersPath());
    }
}
```



### Assertions
* `assertRedirectsTo($path)`
* `assertRenders($viewName)` - asserts that the given view was rendered

* `assertAssignsModel($variable, $modelObject)` - asserts that a model object was assigned to a view
* `assertAssignsValue($variable, $value)`

* `assertRenderedContent()` - returns StringAssert for rendered content.


Sample usage:
```php
$this->assertRenders('Users/index');

$this->assertRedirectsTo(usersPath());

$this->assertRenderedContent()->startsWith('<html>')->matches('/bob\w+/');
```

## Models tests
Ouzo provides `DbTransactionalTestCase` class that takes care of transactions in tests.
This class starts a new transaction before each test case and rolls it back afterwards.

`ModelAssert` allows you to check if two model objects are equal.

```php
class UserTest extends DbTransactionalTestCase {
    /**
     * @test
     */
    public function shouldPersistUser()
    {
        //given
        $user = new User(['name' => 'bob']);

        //when
        $user->insert();

        //then
        $storedUser = User::where(['name' => 'bob'])->fetch();
        Assert::thatModel($storedUser)->isEqualTo($user);
    }
}
```

Model assertions: 
* `Assert::thatModel($storedUser)->isEqualTo($user);` - Compares all attributes. If one model has loaded a relation and other has not, they are considered not equal. Attributes not listed in model's fields are also compared.

* `Assert::thatModel($storedUser)->hasSameAttributesAs($user);` - Compares only attributes listed in Models fields.

## String assertions

Sample usage:
```php
Assert::thatString("Frodo")
     ->startsWith("Fro")->endsWith("do")
     ->contains("rod")->doesNotContain("fro")
     ->hasSize(5);

Assert::thatString("Frodo")->matches('/Fro\w+/');
Assert::thatString("Frodo")->isEqualToIgnoringCase("frodo");
Assert::thatString("Frodo")->isEqualTo("Frodo");
Assert::thatString("Frodo")->isEqualNotTo("asd");
```


## Array assertions
Fluent array assertion to simplify your tests.

Sample usage:
```php
$animals = array('cat', 'dog', 'pig');
Assert::thatArray($animals)->hasSize(3)->contains('cat');
Assert::thatArray($animals)->containsOnly('pig', 'dog', 'cat');
Assert::thatArray($animals)->containsExactly('cat', 'dog', 'pig');
```

Array assertions can also be used to examine array of objects.
Using onProperty:

```php
$object1 = new stdClass();
$object1->prop = 1;

$object2 = new stdClass();
$object2->prop = 2;

$array = array($object1, $object2);
Assert::thatArray($array)->onProperty('prop')->contains(1, 2);
```

Using onMethod:
```php
Assert::thatArray($users)->onMethod('getAge')->contains(35, 24);
```

## Exception assertions
CatchException enables you to write a unit test that checks that an exception is thrown.

```php
//given
$foo = new Foo();

//when
CatchException::when($foo)->method();

//then
CatchException::assertThat()->isInstanceOf("FooException");
```

Available assertions:
* isInstanceOf($exception)
* isEqualTo($exception)
* notCaught()



## Mocking
Ouzo provides a mockito like mocking library that allows you to write tests in BDD or AAA (arrange act assert) fashion.

You can stub method calls:

```php
$mock = Mock::mock();
Mock::when($mock)->method(1)->thenReturn('result');

$result = $mock->method(1);

$this->assertEquals("result", $result);
```

And then verify interactions:

```php
//given
$mock = Mock::mock();

//when
$mock->method("arg");

//then
Mock::verify($mock)->method("arg");
```

Unlike other PHP mocking libraries you can verify interactions ex post facto which is more natural and fits BDD or AAA style.

If you use type hinting and the mock has to be of a type of a Class, you can pass the required type to Mock::mock method.
 
```php
$mock = Mock::mock('Foo');

$this->assertTrue($mock instanceof Foo);
```

You can stub a method to throw an exception;

```php
Mock::when($mock)->method()->thenThrow(new Exception());
```


Verification that a method was not called:

```php
Mock::verify($mock)->neverReceived()->method("arg");
```

Argument matchers:

* Mock::any() - matches any value for an argument at the given position

```php
Mock::verify($mock)->method(1, Mock::any(), "foo");
```

* Mock::anyArgList() - matches any possible arguments. It means that all calls to a given method will be matched.

```php
Mock::verify($mock)->method(Mock::anyArgList());
```

You can stub multiple calls in one call to thenReturn:
```php
$mock = Mock::mock();
Mock::when($mock)->method(1)->thenReturn('result1', 'result2');
Mock::when($mock)->method()->thenThrow(new Exception('1'), new Exception('2'));
```

You can stub a method to return value calculated by a callback function:
```php
Mock::when($mock)->method(Mock::any())->thenAnswer(function (MethodCall $methodCall) {
  return $methodCall->name . ' ' . Arrays::first($methodCall->arguments);
});
```