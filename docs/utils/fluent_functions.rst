FluentFunctions
===============

Methods in ``FluentFunctions`` return instance of ``FluentFunction`` that contains all functions from :doc:`functions`.

----

Create a function that extracts field 'name' form the given argument, then removes prefix 'super', adds ' extra' at the beginning, appends '! ' and surrounds result with ``"***"``.
::

      $function = FluentFunctions::extractField('name')
            ->removePrefix('super')
            ->prepend(' extra')
            ->append('! ')
            ->surroundWith("***");

      $product = new Product(array('name' => 'super phone'));

      $result = Functions::call($function, $product); //=> '*** extra phone! ***'
