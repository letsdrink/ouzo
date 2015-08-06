Suppliers
=========

Static utility methods returning suppliers.

----

memoize
~~~~~~~
Returns a supplier which caches the callback result and returns that value on subsequent calls to ``get()``.

::

    class Command
    {
        public function getNumber()
        {
            return rand();
        }
    }

    $command = new Command();

    $supplier = Suppliers::memoize(function () use ($command) {
        return $command->getNumber(); //returns 1102808477
    });

    echo $supplier->get(); //1102808477
    echo $supplier->get(); //1102808477

----

memoizeWithExpiration
~~~~~~~~~~~~~~~~~~~~~
Returns a supplier which caches the callback result and removes the cached value after specified time.
Subsequent calls to ``get()`` return the cached value if expiration time has not passed.
Time is passed in seconds.

::

    class Command
    {
        public function getNumber()
        {
            return rand();
        }
    }

    $command = new Command();

    $supplier = Suppliers::memoizeWithExpiration(function () use ($command) {
        return $command->getNumber(); //returns 1102808477
    }, 10);

    echo $supplier->get(); //1102808477
    echo $supplier->get(); //1102808477
    //after 10 seconds
    echo $supplier->get(); //1302561906

