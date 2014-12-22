Session
=======

Session is facade for session handling. Session data is stored in files. Path can be set in configuration if you want to change your system's default ($config['session']['path']).

Initializing session
~~~~~~~~~~~~~~~~~~~~
To initialize session use:
::

    Session::startSession();

You don't need to call it if you use Ouzo Controllers - it is done automatically.

Getting session variables by key
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
To get a variable from session use:
::

    $value = Session::get('key');

Getting all session variables
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
To get all session variables use:
::

    $array = Session::all();

Setting session variables
~~~~~~~~~~~~~~~~~~~~~~~~~
To set a variable in session use:
::

    Session::set('key', 'value');

Result is:
::

    array(1) {
      'key' =>
      string(5) "value"
    }

Set methods can be chained:
::

    Session::set('key', 'value')->set('another', 'value');

Pushing session variables (arrays)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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

Removing session variables
~~~~~~~~~~~~~~~~~~~~~~~~~~
To remove a variable from session use:
::

    $value = Session:remove('key');

Checking if session variables exist
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
To check if a variable exists in session use:
::

    $value = Session:has('key');

Flushing session
~~~~~~~~~~~~~~~~
To remove all variables from session just flush it:
::

    Session:flush();

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

Asserts for testing session
~~~~~~~~~~~~~~~~~~~~~~~~~~~
Assert class comes with a handy method to test your session content. Example:
::

    // when
    Session::set('key1', 'value1');
           ->set('key2', 'value2');

    // then
    Assert::thatSession()
          ->hasSize('2')
          ->contains('key2' => 'value2');
