FluentArray
===========

``FluentArray`` provides an interface for manipulating arrays in a chained fashion. It's inspired by FluentIterable from guava library.

Example:

::

    $result = FluentArray::from($users)
                 ->map(Functions::extractField('name'))
                 ->filter(Functions::notEmpty())
                 ->unique()
                 ->toArray();

Example above returns an array of non empty unique names of users.

----

from
~~~~
Returns a FluentArray that wraps the given array.

**Parameters:** ``array $array``

----

map
~~~
Returns a FluentArray that applies function to each element of this FluentArray.

**Parameters:** ``$function``

----

mapKeys
~~~~~~~
Returns a FluentArray that applies $function to each key of this FluentArray.

**Parameters:** ``$function``

----

filter
~~~~~~
Returns a FluentArray that contains only elements that satisfy a predicate.

**Parameters:** ``$function``

----

filterNotBlank
~~~~~~~~~~~~~~
Return a FluentArray that applies function Arrays::filterNotBlank on each of element.

----

filterByKeys
~~~~~~~~~~~~
Returns a FluentArray that contains only elements with keys that satisfy a predicate.

**Parameters:** ``$function``

----

filterByAllowedKeys
~~~~~~~~~~~~~~~~~~~
Returns a FluentArray containing only the given keys.

**Parameters:** ``$allowedKeys``

----

unique
~~~~~~
Returns a FluentArray that contains unique elements.

----

uniqueBy
~~~~~~~~
Removes duplicate values from an array. It uses the given expression to extract value that is compared.

**Parameters:** ``$selector``

**Example:**
::

    $a = new stdClass();
    $a->name = 'bob';

    $b = new stdClass();
    $b->name = 'bob';

    $array = [$a, $b];
    $result = FluentArray::from($array)->uniqueBy('name')->toArray();

**Result:**
::

    Array
    (
        [0] => $b
    )

----

keys
~~~~
Returns a FluentArray that contains array of keys of the original FluentArray.

----

values
~~~~~~
Returns a FluentArray that contains array of values of the original FluentArray.

----

flatten
~~~~~~~
Returns a FluentArray that contains flattened array of the original FluentArray.

----

intersect
~~~~~~~~~
Returns a FluentArray that contains only elements of the original FluentArray that occur in the given $array.

**Parameters:** ``array $array``

----

reverse
~~~~~~~
Returns a FluentArray that contains elements of the original FluentArray in reversed order.

----

toMap
~~~~~
This method creates associative array using key and value functions on array elements.
If ``$valueFunction`` is not given the result will contain original elements as values.

**Parameters:** ``$keyFunction``, ``$valueFunction = null``

**Example:**
::

    $array = range(1, 2);
    $map = FluentArray::from($array)->toMap(function ($elem) {
              return $elem * 10;
           }, function ($elem) {
                return $elem + 1;
           });

**Result:**
::

    Array
    (
        [10] => 2
        [20] => 3
    )

----

toArray
~~~~~~~
Returns elements of this FluentArray as php array.

----

firstOr
~~~~~~~
Returns the first element of this FluentArray or ``$default`` if FluentArray is empty.

**Parameters:** ``$default``

----

toJson
~~~~~~
Encodes FluentArray elements to json.

----

limit
~~~~~
Returns a FluentArray with the first ``$number`` elements of this FluentArray.

**Parameters:** ``$number``

**Example:**
::

    $array = array(1, 2, 3);
    $result = FluentArray::from($array)->limit(2)->toArray();

**Result:**
::

    Array
    (
        [0] => 1,
        [1] => 2,
    )

----

skip
~~~~
Returns a FluentArray that skips its first ``$number`` elements.

**Parameters:** ``$number``

**Example:**
::

    $array = [1, 2, 3];
    $result = FluentArray::from($array)->skip(2)->toArray();

**Result:**
::

    Array
    (
        [0] => 3
    )

sort
~~~~
Returns a FluentArray with its elements sorted using the given comparator

**Parameters:** ``$comparator``

**Example:**
::

    $array = [3, 1, 2];
    $result = FluentArray::from($array)->sort(Comparator::natural())->toArray();

**Result:**
::

    Array
    (
        [0] => 1,
        [1] => 2,
        [2] => 3
    )

