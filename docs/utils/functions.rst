Functions
=========

Static utility methods returning closures.

----

extractId
~~~~~~~~~
Returns a function object that calls ``getId`` method on its argument.

**Example:**
``$ids = Arrays::map($models, Functions::extractId());``

----

extractField
~~~~~~~~~~~~
Returns a function object that returns a value of the given field of its argument.

**Parameters:** ``field``

**Example:**
::

  $users = array(User::new(array('name' => 'bob')), User::new(array('name' => 'john')));
  $names = Arrays::map($users, Functions::extractField('name'));

----

extractFieldRecursively
~~~~~~~~~~~~~~~~~~~~~~~
Returns a function object that returns a value of the given nested field of its argument.

**Parameters:** ``$fields``

**Example:**
::

  $object = new stdClass();
  $object->field1 = new stdClass();
  $object->field1->field2 = 'value';

  $fun = Functions::extractFieldRecursively('field1->field2');
  $result = $fun($object);

**Result:** ``value``

**Example:**
``$groupNames = Arrays::map($users, Functions::extractFieldRecursively('group->name'));``

.. note::

  It can also call functions:
  ``$groupFullNames = Arrays::map($users, Functions::extractFieldRecursively('group->getFullName()'));``

----

extractExpression
~~~~~~~~~~~~~~~~~
Returns a function object that returns a result of the expression evaluated for its argument.
It's a more efficient equivalent of :func:`extractField` and :func:`extractFieldRecursively` (it examines the given expression and returns the most suitable function).

If ``$expression`` is a function object, it is returned unchanged.

**Parameters:** ``$expression``

----

identity
~~~~~~~~
Returns a function object that always returns the argument.

**Example:**
::

  $fun = Functions::identity()
  $result = $fun('bob');

**Result:** ``bob``

----

constant
~~~~~~~~
Creates a function that returns value for any input.

**Example:**
::

  $fun = Functions::constant('john')
  $result = $fun('bob');

**Result:** ``john``

----

throwException
~~~~~~~~~~~~~~
Creates a function that throws $exception for any input.

**Example:**
::

  $fun = Functions::throwException(new Exception('error'))
  $result = $fun('bob');

**Throws:** ``Exception('error')``

----

trim
~~~~
Returns a function object that trims its arguments.

----

not
~~~
Returns a function object that negates result of supplied predicate.

**Parameters:** ``$predicate``

**Example:**
``$isNotArrayFunction = Functions::not(Functions::isArray());``

----

isArray
~~~~~~~
Returns a function object (predicate) that returns true if its argument is an array.

----

isInstanceOf
~~~~~~~~~~~~
Returns a function object (predicate) that returns true if its argument is an instance of the given type.

**Parameters:** ``$type``

----

prepend
~~~~~~~
Returns a function object that prepends the given prefix to its arguments.

**Parameters:** ``$prefix``

----

append
~~~~~~
Returns a function object that appends the given suffix to its arguments.

**Parameters:** ``$suffix``

----

notEmpty
~~~~~~~~
Returns a function object (predicate) that returns true if its argument is not empty.

----

notBlank
~~~~~~~~
Returns a function object (predicate) that returns true if its argument is not blank.

----

removePrefix
~~~~~~~~~~~~
Returns a function object that removes the given prefix from its arguments.

**Parameters:** ``$prefix``

----

startsWith
~~~~~~~~~~
Returns a function object (predicate) that returns true if its argument starts with the given prefix.

**Parameters:** ``$prefix``

----

formatDateTime
~~~~~~~~~~~~~~
Returns a function object that format date time its arguments.

**Parameters:** ``$format = Date::DEFAULT_TIME_FORMAT``

----

compose
~~~~~~~
Returns the composition of two functions.
Composition is defined as the function h such that h(a) == A(B(a)) for each a.

**Parameters:** ``$functionA``, ``$functionB``

----

toString
~~~~~~~~
Returns a function object that calls ``Objects::toString`` on its argument.

----

.. _functions-extract:

extract
~~~~~~~
Fluent builder for a callable that extracts a value from its argument.

**Parameters:** ``$type`` - optional type hint for PhpStorm dynamicReturnType plugin.

Let's assume that you have a User class that has a list of addresses. Each address has a type (like: home, invoice etc.) and User has getAddress($type) method.

Now, let's write a code that given a list of users, returns a lists of cities from users' home addresses.
::

  $cities = Arrays::map($users, function($user) {
    return $user>getAddress('home')->city;
  });

It gets more complicated when some users don't have home address:
::

  $cities = Arrays::map($users, function($user) {
    $address = $user>getAddress('home');
    return $address? $address->city : null;
  });

We can write it in one line using Functions::extract:

``$cities=Arrays::map($users, Functions::extract()->getAddress('home')->city);``

Additionally, if you use PhpStorm dynamicReturnType plugin you can pass type as the first argument of Functions::extract.

``Arrays::map($users, Functions::extract('User')->getAddress('home')->city);``

::

  $cities = Arrays::map($users, Functions::extract('User')->...
  //ctrl+space will show you all methods/properties of the User class

----

surroundWith
~~~~~~~~~~~~
Returns a function object that surround with given character its arguments.

**Parameters:** ``$character``

----

equals
~~~~~~
**Parameters:** ``$object``

----

notEquals
~~~~~~~~~
**Parameters:** ``$object``
