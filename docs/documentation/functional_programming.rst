Functional programming
======================

Ouzo provides many utility classes that facilitate functional programming in php.


* :doc:`../utils/arrays` contains facade for php arrays functions. You will never wonder if array_filter has array or closure as the first parameter.
* :doc:`../utils/functions` contains static utility methods returning closures that can be used with Arrays and FluentArray.
* :doc:`../utils/fluent_array` provides an interface for manipulating arrays in a chained fashion.
* :doc:`../utils/fluent_iterator` provides an interface for manipulating iterators in a chained fashion.
* :doc:`../utils/fluent_functions` provides an interface for composing functions in a chained fashion.


Example 1
~~~~~~~~~
Let's assume that you have a User class that has a method isCool. You have an array of users and want to check if any of them is cool.


Pure php:

::

    function isAnyCool($users) {
        foreach($users as $user) {
            if ($user->isCool()) {
                return true;
            }
        }
        return false;
    }


Ouzo:

::

    function isAnyCool($users) {
        return Arrays::any($users, function($user) { return $user->isCool(); });
    }

or using Functions::extract():

::

    function isAnyCool($users) {
        return Arrays::any($users, Functions::extract()->isCool());
    }


Similarly, you may want to check if all of them are cool:

::

    $allCool = Arrays::all($users, Functions::extract()->isCool());


.. seealso::

    :ref:`Arrays::groupBy <Arrays-groupBy>`

    :ref:`Arrays::toMap <Arrays-toMap>`

Example 2
~~~~~~~~~

Let's assume that you have a User class that has a list of addresses. Each address has a type (like: home, invoice etc.) and User has ``getAddress($type)`` method.

Now, let's write a code that given a list of users, returns a lists of unique non-empty cities from users' home addresses.

----

Pure php:

::

    $cities = array_unique(array_filter(array_map(function($user) {
       $address = $user>getAddress('home');
       return $address? $address->city : null;
    }, $users)));

Ouzo:

::

    $cities = FluentArray::from($users)
             ->map(Functions::extract()->getAddress('home')->city)
             ->filter(Functions::notEmpty())
             ->unique()
             ->toArray();

.. seealso::

    :doc:`../utils/fluent_array`

    :ref:`Functions::extract <functions-extract>`


Example 3
~~~~~~~~~

If the array/iterator is very long and you are interested only in a small subset or processing is time consuming, you may want to use FluentIterator so that all operations are performed lazily (and only if necessary).
::

    $activityReports = FluentIterator::from($users)
             ->filter(activeInLastMonth())
             ->map(createActivityReport())
             ->limit(10)
             ->toArray();

.. seealso::

    :doc:`../utils/fluent_iterator`





Composing functions
~~~~~~~~~~~~~~~~~~~

Class ``FluentFunctions`` allows you to easily compose functions from ``Functions``.

::

    $usersWithSurnameStartingWithB =
          Arrays::filter($users, FluentFunctions::extractField('surname')->startsWith('B'));

is equivalent of:

::

    $usersWithSurnameStartingWithB = Arrays::filter($users, function($user) {
        $extractField = Functions::extractField('name');
        $startsWith = Functions::startsWith('B');
        return $startsWith($extractField($product));
    });

Another example:

::

    $bobs = Arrays::filter($users, FluentFunctions::extractField('name')->equals('Bob'));

.. seealso::

    :doc:`../utils/fluent_functions`
