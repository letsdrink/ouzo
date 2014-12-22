FluentArray
===========

FluentArray provides an interface for manipulating arrays in a chained fashion.
It's inspired by FluentIterable from guava library.
 
**Example**:

::

    $result = FluentArray::from($users)
                 ->map(Functions::extractField('name'))
                 ->filter(Functions::notEmpty())
                 ->unique()
                 ->toArray();

Example above returns an array of non empty unique names of users.

Methods


from($array)
~~~~~~~~~~~~

Returns a FluentArray that wraps the given array.

map($function)
~~~~~~~~~~~~~~

Returns a FluentArray that applies function to each element of this FluentArray.

mapKeys($function)
~~~~~~~~~~~~~~~~~~

Returns a FluentArray that applies $function to each key of this FluentArray.

filter($predicate)
~~~~~~~~~~~~~~~~~~

Returns a FluentArray that contains only elements that satisfy a predicate.

filterByKeys($predicate)
~~~~~~~~~~~~~~~~~~~~~~~~

Returns a FluentArray that contains only elements which keys that satisfy a predicate.

unique
~~~~~~

Returns a FluentArray that contains unique elements.

keys
~~~~

Returns a FluentArray that contains array of keys of the original FluentArray.

values
~~~~~~

Returns a FluentArray that contains array of values of the original FluentArray.

flatten
~~~~~~~

Returns a FluentArray that contains flattened array of the original FluentArray.

reverse
~~~~~~~

Returns a FluentArray that contains elements of the original FluentArray in reversed order.

intersect($array)
~~~~~~~~~~~~~~~~~

Returns a FluentArray that contains only elements of the original FluentArray that occur in the given $array.
toMap($keyFunction, $valueFunction = null)
This method creates associative array using key and value functions on array elements.
If ``$valueFunction`` is not given the result will contain original elements as values.

::

    $array = range(1, 2);
    $map = FluentArray::from($array)->toMap(function ($elem) {
              return $elem * 10;
           }, function ($elem) {
                return $elem + 1;
           });

Result:
::

    Array
    (
        [10] => 2
        [20] => 3
    )

uniqueBy($expression)
~~~~~~~~~~~~~~~~~~~~~

Removes duplicate values from an array. It uses the given expression to extract value that is compared.

::

    $a = new stdClass();
    $a->name = 'bob';

    $b = new stdClass();
    $b->name = 'bob';

    $array = array($a, $b);
    $result = FluentArray::from($array)->uniqueBy('name')->toArray();

Result:
::

    Array
    (
        [0] => $b
    )

toArray
~~~~~~~

Returns elements of this FluentArray as php array.

toJson
~~~~~~
Encodes FluentArray elements to json.

firstOr($default)
~~~~~~~~~~~~~~~~~

Returns the first element of this FluentArray or ``$default`` if FluentArray is empty.

skip($number)
~~~~~~~~~~~~~

Returns a FluentArray that skips its first $number elements.

::

    $array = array(1, 2, 3);
    $result = FluentArray::from($array)->skip(2)->toArray();

Result:
::

    Array
    (
        [0] => 3
    )

limit($number)
~~~~~~~~~~~~~~

Returns a FluentArray with the first $number elements of this FluentArray.

::

    $array = array(1, 2, 3);
    $result = FluentArray::from($array)->limi(2)->toArray();

Result:
::

    Array
    (
        [0] => 1,
        [1] => 2,
    )
