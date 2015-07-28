FluentIterator
==============


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

fromGenerator
~~~~~~~~~~~~~
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

currentOr
~~~~~~~~~
Returns the current element or defaultValue if the current position is not valid.

**Parameters:** ``$default``

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
