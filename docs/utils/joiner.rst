Joiner
======

Helper to join array elements into string using the previously configured separator.

----

on
~~
Static method to create a ``Joiner`` object and defining separator.

**Parameters:** ``$separator``

----

join
~~~~
Returns a string containing array elements joined using the previously configured separator.

**Parameters:** ``array $array``

**Example:**
::

    $result = Joiner::on(', ')->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

**Result:** ``'A, B, C'``

----

skipNulls
~~~~~~~~~
Returns a ``Joiner`` that skips null elements.

**Example:**
::

    $result = Joiner::on(', ')->skipNulls()->join(array('A', null, 'C'));

**Result:** ``'A, C'``

----

map
~~~
Returns a ``Joiner`` that transforms array elements before joining.

**Example:**
::

    $result = Joiner::on(', ')->map(function ($key, $value) {
            return "$key => $value";
        })->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

**Result:**: ``'1 => A, 2 => B, 3 => C'``

----

mapValues
~~~~~~~~~
Returns a ``Joiner`` that transforms array values before joining.

**Example:***
::

    $result = Joiner::on(', ')->mapValues(function ($value) {
            return "val = $value";
        })->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

**Result:** ``'val = A, val = B, val = C'``
