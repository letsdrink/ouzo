FluentIterator
==============

Interface for manipulating iterators in a chained fashion.
It's inspired by FluentIterable from guava library.

::

    $result = FluentIterator::fromArray(array(1, 2, 3))
             ->cycle()
             ->limit(10)
             ->reindex()
             ->toArray(); // array(1, 2, 3, 1, 2, 3, 1, 2, 3, 1)

::

    $result = FluentIterator::fromFunction(Functions::random(0, 10))
             ->limit(10)
             ->toArray(); // returns array of 10 random numbers between 0 and 10 (inclusive)

All methods are applied lazily during iteration or call to ``toArray``.


FluentIterator works great with php 5.5 generators:

::

    function fibonacci() {
        $i = 0;
        $k = 1;
        while(true) {
            yield $k;
            $k = $i + $k;
            $i = $k - $i;
        }
    }

Get 10th Fibonacci number:

::

    $number = FluentIterator::from(fibonacci())->skip(9)->first();

Display first ten fibonacci numbers that are greater than 100:
::

    $iterator = FluentIterator::from(fibonacci())
        ->filter(function($number) {
            return $number > 100;
        })
        ->limit(10);

    foreach($iterator as $number) {
        echo $number, ", ";
    }

----

from
~~~~
Returns a fluent iterator that wraps ``$iterator``

**Parameters:** ``Iterator $iterator``

----

fromArray
~~~~~~~~~
Returns a fluent iterator for ``$array``.

**Parameters:** ``array $array``

----

fromFunction
~~~~~~~~~~~~
Returns a fluent iterator that uses $function to generate elements.
``$function`` takes one argument which is the current position of the iterator.

**Parameters:** ``callable $function``

----

cycle
~~~~~
Returns a fluent iterator that cycles indefinitely over the elements of this fluent iterator.

----

batch
~~~~~
Returns a fluent iterator returning elements of this fluent iterator grouped in chunks of ``$chunkSize``

**Parameters:** ``$chunkSize``

----

filter
~~~~~~
Returns a fluent iterator returning elements of this fluent iterator that satisfy a predicate.

**Parameters:** ``callable $predicate``

----

map
~~~
Returns a fluent iterator that applies function to each element of this fluent iterator.

**Parameters:** ``callable $function``

----

firstOr
~~~~~~~
Returns the first element or defaultValue if the iterator is empty.

**Parameters:** ``$default``

----

first
~~~~~
Returns the first element in iterator or throws an Exception if iterator is empty

----

limit
~~~~~
Returns a fluent iterator returning the first ``$number`` elements of of this fluent iterator.

**Parameters:** ``$number``

----

skip
~~~~
Returns a fluent iterator returning all but first ``$number`` elements of this fluent iterator.

**Parameters:** ``$number``

----

reindex
~~~~~~~
Returns an iterator that indexes elements numerically starting from 0

----

toArray
~~~~~~~
Copies elements of this fluent iterator into an array.
