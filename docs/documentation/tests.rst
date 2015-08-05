Tests
=====

Controller test case
~~~~~~~~~~~~~~~~~~~~
Ouzo provides ``ControllerTestCase`` which allows you to verify that:

* there's a route for a given url
* controllers methods work as expected
* views are rendered without errors

::

    <?php
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
            $this->post('/users', [
                'user' => [
                    'login' => 'login'
                ]]
            );

            //then
            $this->assertRedirectsTo(usersPath());
        }
    }

Methods
-------

* ``get($url)`` - mock GET request for given url
* ``post($url, $data)`` - mock POST request with data for given url
* ``put($url, $data)`` - mock PUT request with data for given url
* ``patch($url)`` - mock PATCH request for given url
* ``delete($url)``- mock DELETE request for given url
* ``getAssigned($name)`` - get value of $name variable assigned to the rendered view.
* ``getRenderedJsonAsArray()`` - get returned JSON as array
* ``getResponseHeaders()`` - get all response header

Assertions
----------

* ``assertRedirectsTo($path)``
* ``assertRenders($viewName)`` - asserts that the given view was rendered
* ``assertAssignsModel($variable, $modelObject)`` - asserts that a model object was assigned to a view
* ``assertDownloadsFile($file)``
* ``assertAssignsValue($variable, $value)``
* ``assertRenderedContent()`` - returns StringAssert for rendered content.
* ``assertRenderedJsonAttributeEquals($attribute, $equals)``
* ``assertResponseHeader($expected)``

----

Database test case
~~~~~~~~~~~~~~~~~~
Ouzo provides ``DbTransactionalTestCase`` class that takes care of transactions in tests.
This class starts a new transaction before each test case and rolls it back afterwards.

::

    <?php
    class UserTest extends DbTransactionalTestCase
    {
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
            $this->assertEquals('bob', $storedUser->name);
        }
    }

----

Model assertions
~~~~~~~~~~~~~~~~
``Assert::thatModel`` allows you to check if two model objects are equal.

Sample usage
------------

::

    <?php
    class UserTest extends DbTransactionalTestCase
    {
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

Assertions
----------

* ``isEqualTo($expected)`` - compares all attributes. If one model has loaded a relation and other has not, they are considered not equal. Attributes not listed in model's fields are also compared
* ``hasSameAttributesAs($expected)`` - compares only attributes listed in Models fields

----

String assertions
~~~~~~~~~~~~~~~~~
``Assert::thatString`` allows you to check strings as a fluent assertions.

Sample usage
------------
::

    Assert::thatString("Frodo")
         ->startsWith("Fro")->endsWith("do")
         ->contains("rod")->doesNotContain("fro")
         ->hasSize(5);

    Assert::thatString("Frodo")->matches('/Fro\w+/');
    Assert::thatString("Frodo")->isEqualToIgnoringCase("frodo");
    Assert::thatString("Frodo")->isEqualTo("Frodo");
    Assert::thatString("Frodo")->isEqualNotTo("asd");

Assertions
----------

* ``contains($substring)`` - check that string contains substring
* ``doesNotContain($substring)`` - check that string does not contains substring
* ``startsWith($prefix)`` - check that string is start with prefix
* ``endsWith($postfix)`` - check that string is end with postfix
* ``isEqualTo($string)`` - check that string is equal to expected
* ``isEqualToIgnoringCase($string)`` - check that string is equal to expected (case insensitive)
* ``isNotEqualTo($string)`` - check that string not equal to expected
* ``matches($regex)`` - check that string is fit to regexp
* ``hasSize($length)`` - check string length
* ``isNull()`` - check a string is null
* ``isNotNull()`` - check a string is not null
* ``isEmpty()`` - check a string is empty
* ``isNotEmpty()`` - check a string is not empty

----

Array assertions
~~~~~~~~~~~~~~~~
``Assert::thatArray`` is a fluent array assertion to simplify your tests.

Sample usage
------------
::

    <?php
    $animals = ['cat', 'dog', 'pig'];
    Assert::thatArray($animals)->hasSize(3)->contains('cat');
    Assert::thatArray($animals)->containsOnly('pig', 'dog', 'cat');
    Assert::thatArray($animals)->containsExactly('cat', 'dog', 'pig');

.. note::

    Array assertions can also be used to examine array of objects. Methods to do this is ``onProperty`` and ``onMethod``.

Using ``onProperty``:

::

    <?php
    $object1 = new stdClass();
    $object1->prop = 1;

    $object2 = new stdClass();
    $object2->prop = 2;

    $array = [$object1, $object2];
    Assert::thatArray($array)->onProperty('prop')->contains(1, 2);

Using ``onMethod``:

::

    Assert::thatArray($users)->onMethod('getAge')->contains(35, 24);

Assertions
----------

* ``contains($element ..)`` - vararg elements to examine that array contains them
* ``containsOnly($element ..)`` - vararg elements to examine that array contains **only** them
* ``containsExactly($element ..)`` - vararg elements to examine that array contain **exactly** elements in pass order
* ``hasSize($expectedSize)`` - check size of the array
* ``isNotNull()`` - check the array is not null
* ``isEmpty()`` - check the array is empty
* ``isNotEmpty()`` - check the array is not empty
* ``containsKeyAndValue($elements)``
* ``containsSequence($element ..)`` - check that vararg sequence is exists in the array
* ``excludes($element ..)``
* ``hasEqualKeysRecursively(array $array)``
* ``isEqualTo($array)``

----

Exception assertions
~~~~~~~~~~~~~~~~~~~~
CatchException enables you to write a unit test that checks that an exception is thrown.

Sample usage
------------
::

    //given
    $foo = new Foo();

    //when
    CatchException::when($foo)->method();

    //then
    CatchException::assertThat()->isInstanceOf("FooException");

Assertions
----------

* ``isInstanceOf($exception)``
* ``isEqualTo($exception)``
* ``notCaught()``
* ``hasMessage($message)``

----

.. _session-assertions:

Session assertions
~~~~~~~~~~~~~~~~~~
``Assert::thatSession`` class comes with a handy method to test your session content.

Sample usage
------------
::

    // when
    Session::set('key1', 'value1');
           ->set('key2', 'value2');

    // then
    Assert::thatSession()
          ->hasSize('2')
          ->contains('key2' => 'value2');

.. note::

    This assert has the same method as ``Assert::thatArray``.

----

Testing time-dependent code
~~~~~~~~~~~~~~~~~~~~~~~~~~~

We do recommend you to use Clock instead of DateTime.
Clock provides time travel and time freezing capabilities, making it simple to test time-dependent code.

::

    //given
    Clock::freeze('2011-01-02 12:34');

    //when
    $result = Clock::nowAsString('Y-m-d');

    //then
    $this->assertEquals('2011-01-02', $result);

.. seealso::

    :doc:`../utils/clock`

Mocking
~~~~~~~

Ouzo provides a Mockito like mocking library that allows you to write tests in BDD (given when then) or AAA (arrange act assert) fashion.

You can stub method calls:

::

    $mock = Mock::create();
    Mock::when($mock)->method(1)->thenReturn('result');

    $result = $mock->method(1);

    $this->assertEquals("result", $result);

And then verify interactions:

::

    //given
    $mock = Mock::create();

    //when
    $mock->method("arg");

    //then
    Mock::verify($mock)->method("arg");

Unlike other PHP mocking libraries you can verify interactions ex post facto which is more natural and fits BDD or AAA style.

.. note::

    Mock::mock() is an alias for Mock::create(). You can use those methods interchangeably.

If you use type hinting and the mock has to be of a type of a Class, you can pass the required type to ``Mock::create`` method.

::

    $mock = Mock::create('Foo');

    $this->assertTrue($mock instanceof Foo);

You can stub a method to throw an exception;

::

    Mock::when($mock)->method()->thenThrow(new Exception());

Verification that a method was not called:

::

    Mock::verify($mock)->neverReceived()->method("arg");

Making sure that there were no interactions:

::

    Mock::verifyZeroInteractions($mock);

You can stub multiple calls in one call to thenReturn:

::

    $mock = Mock::create();
    Mock::when($mock)->method(1)->thenReturn('result1', 'result2');
    Mock::when($mock)->method()->thenThrow(new Exception('1'), new Exception('2'));

Both thenReturn and thenThrow accept multiples arguments that will be returned/thrown in subsequent calls to a stubbed method.

::

    $mock = Mock::create();

    Mock::when($mock)->method()->thenReturn('result1', 'result2');

    $this->assertEquals("result1", $mock->method());
    $this->assertEquals("result2", $mock->method());


You can stub a method to return value calculated by a callback function:

::

    Mock::when($mock)->method(Mock::any())->thenAnswer(function (MethodCall $methodCall) {
      return $methodCall->name . ' ' . Arrays::first($methodCall->arguments);
    });


Argument matchers
-----------------

* Mock::any() - matches any value for an argument at the given position

::

    Mock::verify($mock)->method(1, Mock::any(), "foo");

* Mock::anyArgList() - matches any possible arguments. It means that all calls to a given method will be matched.

::

    Mock::verify($mock)->method(Mock::anyArgList());

* Mock::argThat() - returns an instance of FluentArgumentMatcher that can chain methods from :doc:`../utils/functions`.

::

    Mock::verify($mock)->method(Mock::argThat()->extractField('name')->equals('Bob'));

::

    Mock::verify($mock)->method('first arg', Mock::argThat()->isInstanceOf('Foo'));


In rare cases, you may need to write your own argument matcher:

::

    class MyArgumentMatcher implements Ouzo\Tests\Mock\ArgumentMatcher {
        public function matches($argument) {
            return $argument->name == 'Bob' || $argument->surname == 'Smith';
        }
    }

    Mock::verify($mock)->method(new MyArgumentMatcher());

Stream stubbing
~~~~~~~~~~~~~~~

In some cases you may need to to stub stream wrappers e.g. ``php://input``. Ouzo provides a special class for this ``StreamStub``.

* First, you have to register your wrapper:

::

    StreamStub::register('you_wrapper_name');

* Then write something to the wrapper:

::

    StreamStub::$body = 'some content';

* When you're finished using stream you must unregistered it:

::

    StreamStub::unregister();

Comprehensive example:

::

    StreamStub::register('some_name');
    StreamStub::$body = '{"name":"jonh","id":123,"ip":"127.0.0.1"}';

    $object = YourClass::readJsonFromWrapper('some_name://input');

    $this->assertEquals('john', $object->name);
    StreamStub::unregister();
