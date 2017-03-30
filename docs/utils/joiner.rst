Joiner
======

Joins array text elements together with a separator. Returns a string.

----

on
~~
Static method to create a ``Joiner`` object and define a separator.

**Parameters:** ``$separator``

----

join
~~~~
Returns a string containing array elements joined together with a separator.

**Parameters:** ``array $array``

**Example:**
::

    $result = Joiner::on(', ')->join([1 => 'A', 2 => 'B', 3 => 'C']);

**Result:** ``'A, B, C'``

----

skipNulls
~~~~~~~~~
Returns a ``Joiner`` that skips null elements.

**Example:**
::

    $result = Joiner::on(', ')->skipNulls()->join(['A', null, 'C']);

**Result:** ``'A, C'``

----

map
~~~
Returns a ``Joiner`` that transforms array elements before joining.

**Example:**
::

    $result = Joiner::on(', ')->map(function ($key, $value) {
            return "$key => $value";
        })->join([1 => 'A', 2 => 'B', 3 => 'C']);

**Result:**: ``'1 => A, 2 => B, 3 => C'``

----

mapValues
~~~~~~~~~
Returns a ``Joiner`` that transforms array values before joining.

**Example:***
::

    $result = Joiner::on(', ')->mapValues(function ($value) {
            return "val = $value";
        })->join([1 => 'A', 2 => 'B', 3 => 'C']);

**Result:** ``'val = A, val = B, val = C'``
