Config
======

Ouzo has primary configurations locations in ``config`` directory. Inside this folder exists configuration for *prod* and *test*
environments.

----

Custom config in Bootstrap
~~~~~~~~~~~~~~~~~~~~~~~~~~
Ouzo has options to add custom config class, which can override default defined config values.

.. note::

    **TODO:** Extract interface for custom configs.

::

    $bootstrap = new Bootstrap();
    $bootstrap->addConfig(new MyNewConfig());
    $bootstrap->runApplication();

----

Config in session
~~~~~~~~~~~~~~~~~
Ouzo can handle config per user (using session mechanism). To override or set config value you must add value to ``$_SESSION['config']`` e.g.:

::

    Session::set('config', 'db', 'host', '127.0.0.1');

----

Override rules
~~~~~~~~~~~~~~
Thus config may be loaded form multiple locations there are rules for overriding:

Default config is override by the custom config.

**Example:**
::

    $default['db']['host'] = 'localhost';
    $default['db']['port'] = '5432';

    $custom['db']['port'] = '1122';

**Result (after override):**
::

    Array
    (
        [db] => Array
            (
                [host] => localhost
                [port] => 1122
            )

    )

Override default config by the custom can be also override by the session values.

**Example:**
::

    $default['db']['host'] = 'localhost';
    $default['db']['port'] = '5432';

    $custom['db']['port'] = '1122';

    Session::set('config', 'db', 'host', '127.0.0.1');

**Result (after override):**
::

    Array
    (
        [db] => Array
            (
                [host] => 127.0.0.1
                [port] => 1122
            )

    )

----

Methods
~~~~~~~

getValue
--------
Returns nested config value. If value does not exist it will return empty array.

**Parameters:** ``string $keys..``

**Example:**
::

    $host = Config::getValue('db', 'host'); //search $config['db']['host'] = 'localhost';

**Result:** ``localhost``

----

getPrefixSystem
---------------
Returns defined prefix system.

**Example:**
::

    //$config['global']['prefix_system'] = 'my_super_system';
    $prefix = Config::getPrefixSystem();

**Result:** ``my_super_system``

----

all
---
Returns all defined config parameters.

----

registerConfig
--------------

.. note::

    **TODO:** Extract interface for custom configs.

----

overrideProperty
----------------
Override config property during runtime, may be useful in tests.

**Parameters:** ``string $keys..``

**Example:**
::

    //$config['key']['sub_key'] = 'value';
    Config::overrideProperty('key', 'sub_key')->with('new value');
    $value = Config::getValue('key', 'sub_key');

**Result:** ``new value``

----

clearProperty
-------------
Clear override property to the default value.

**Parameters:** ``string $keys..``

**Example:**
::

    //$config['key']['sub_key'] = 'value';
    Config::overrideProperty('key', 'sub_key')->with('new value');
    Config::clearProperty('key', 'sub_key');

**Result:** ``value``

----

revertProperty
--------------
Revert config last override value.

**Parameters:** ``string $keys..``

**Example:**
::

    //$config['key']['sub_key'] = 'value';
    Config::overrideProperty('key1', 'sub_key')->with('first');
    Config::overrideProperty('key1', 'sub_key')->with('second');
    Config::revertProperty('key1', 'sub_key');

**Result:** ``first``
