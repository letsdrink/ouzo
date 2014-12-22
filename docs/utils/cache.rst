Cache
=====

Simple request scope cache.

get
~~~

Returns object from cache.
If there's no object for the given key and $functions is passed, $function result will be stored in cache under the given key.
**Parameters:** ``$key``, ``$function`` (optional)

**Example:**
::

    $countries = Cache::get("countries", function() {
        //expensive computation that returns a list of countries
        return Country::all();
    })

put
~~~

Stores the given object in the cache.

**Parameters:** ``$key``, ``$object``

contains
~~~~~~~~

Returns true if cache contains an object for the given key.

**Parameters:** ``$key``

memoize
~~~~~~~

Caches the result of the given closure using filename:line as a key.

**Parameters:** ``$function``

**Example:**
::

    $countries = Cache::memoize(function() {
        //expensive computation that returns a list of countries
        return Country::all();
    })
