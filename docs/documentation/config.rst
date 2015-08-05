Config
======

Ouzo has primary configurations locations in ``config`` directory. Inside this folder exists configuration for *prod* and *test*
environments.

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

----

Adding config in Bootstrap
~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    $bootstrap = new Bootstrap();
    $bootstrap->addConfig(new PrimaryConfig());
    $bootstrap->addConfig(new RuntimeConfig());
    $bootstrap->runApplication();

----

Config stored in session
~~~~~~~~~~~~~~~~~~~~~~~~

----

Requirement config parameters
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
