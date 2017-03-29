FluentFunctions
===============

Fluent interface for function composition.

Methods in ``FluentFunctions`` return instance of ``FluentFunction`` that contains all functions from :doc:`functions`.

Calls to FluentFunction can be chained. The resultant function calls chained function in the order they were specified.

For example:
::

      $functionC = FluentFunctions::functionA()->functionB();


results in a functionC such that for each argument x functionC(x) == functionB(functionA(x)).


----

Example
~~~~~~~

Let's create a function that extracts field 'name' from the given argument, then removes prefix 'super', adds ' extra' at the beginning, appends '! ' and surrounds result with ``"***"``.
::

      $function = FluentFunctions::extractField('name')
            ->removePrefix('super')
            ->prepend(' extra')
            ->append('! ')
            ->surroundWith("***");

      $product = new Product(['name' => 'super phone']);

      $result = Functions::call($function, $product); //=> '*** extra phone! ***'
