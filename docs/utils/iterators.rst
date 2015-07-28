Iterators
=========

forArray
~~~~~~~~
Returns an iterator containing the elements of ``$array``.

**Parameters:** ``array $array``

----

generate
~~~~~~~~
Returns an iterator that uses ``$function`` to generate elements.
``$function`` takes one argument which is the current position of the iterator.

**Parameters:** ``callable $function``

----

cycle
~~~~~
Returns an iterator that cycles indefinitely over the elements of ``$iterator``.

**Parameters:** ``Iterator $iterator``

----

batch
~~~~~
Returns the elements of ``$iterator`` grouped in chunks of ``$chunkSize``

**Parameters:** ``Iterator $iterator``, ``$chunkSize``

----

filter
~~~~~~
Returns the elements of ``$iterator`` that satisfy a predicate.

**Parameters:** ``Iterator $iterator``, ``callable $predicate``

----

map
~~~
Returns an iterator that applies function to each element of ``$iterator``.

**Parameters:** ``Iterator $iterator``,``callable $function``

----

firstOr
~~~~~~~
Returns the first element or defaultValue if the iterator is empty.

**Parameters:** ``Iterator $iterator``,``$default``

first
~~~~~
Returns the first element in iterator or throws an Exception if iterator is empty

----

limit
~~~~~
Creates an iterator returning the first ``$number`` elements of the given iterator.

**Parameters:** ``Iterator $iterator``,``$number``

----

skip
~~~~
Creates an iterator returning all but first ``$number`` elements of the given iterator.

**Parameters:** ``Iterator $iterator``,``$number``

----

reindex
~~~~~~~
Returns an iterator that indexes elements numerically starting from 0

**Parameters:** ``Iterator $iterator``

----

toArray
~~~~~~~
Copies an iterator's elements into an array.

**Parameters:** ``Iterator $iterator``
