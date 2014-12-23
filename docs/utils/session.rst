Session
=======

Session is facade for session handling. Session data is stored in files. Path can be set in configuration if you want to change your system's default ($config['session']['path']).

----

startSession
~~~~~~~~~~~~
To initialize session use:
::

    Session::startSession();

.. note::

    You don't need to call it if you use Ouzo Controllers - it is done automatically.

----

get
~~~
To get a variable from session use:
::

    $value = Session::get('key');

----

all
~~~
To get all session variables use:
::

    $array = Session::all();

----

set
~~~
To set a variable in session use:
::

    Session::set('key', 'value');

Result is:
::

    array(1) {
      'key' =>
      string(5) "value"
    }

.. note::

    Set methods can be chained:
    ::

        Session::set('key', 'value')->set('another', 'value');

----

push
~~~~
To add an element to an array stored in session use:

::

    Session::push('key', 'value1');
    Session::push('key', 'value2');

Result is:
::

    array(1) {
      'key' =>
      array(2) {
        [0] =>
        string(6) "value1"
        [1] =>
        string(6) "value2"
      }
    }

----

remove
~~~~~~
To remove a variable from session use:
::

    $value = Session:remove('key');

----

has
~~~
To check if a variable exists in session use:
::

    $value = Session:has('key');

----

flush
~~~~~
To remove all variables from session just flush it:
::

    Session:flush();

----

Nested keys
~~~~~~~~~~~
All session handling methods (except of all and flush) support nested keys e.g.
::

    Session::get('key1', 'key2', 'value');
    Session::set('key1', 'key2', 'value');
    Session::push('key1', 'key2', 'value');
    Session::remove('key1', 'key2');
    Session::has('key1', 'key2');

You can specify as many keys as you want. Last argument in get, set and push is the value.

.. seealso::

    :ref:`Session assertions <session-assertions>`
