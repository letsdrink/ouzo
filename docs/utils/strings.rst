Strings
=======

underscoreToCamelCase
~~~~~~~~~~~~~~~~~~~~~
Changes underscored string to the camel case.

**Parameters:** ``$string``

**Example:**
::

    $string = 'lannisters_always_pay_their_debts';
    $camelcase = Strings::underscoreToCamelCase($string);

**Result:** ``LannistersAlwaysPayTheirDebts``

----

camelCaseToUnderscore
~~~~~~~~~~~~~~~~~~~~~
Changes camel case string to underscored.

**Parameters:** ``$string``

**Example:**
::

    $string = 'LannistersAlwaysPayTheirDebts';
    $underscored = Strings::camelCaseToUnderscore($string);

**Result:** ``lannisters_always_pay_their_debts``

----

removePrefix
~~~~~~~~~~~~
Returns a new string without the given prefix.

**Parameters:** ``$string``, ``$prefix``

**Example:**
::

    $string = 'prefixRest';
    $withoutPrefix = Strings::removePrefix($string, 'prefix');

**Result:** ``Rest``

----

removePrefixes
~~~~~~~~~~~~~~
Removes prefixes defined in array from string.

**Parameters:** ``$string``, ``array $prefixes``

**Example:**
::

    $string = 'prefixRest';
    $withoutPrefix = Strings::removePrefixes($string, array('pre', 'fix'));

**Result:** ``Rest``

----

removeSuffix
~~~~~~~~~~~~
Returns a new string without the given suffix.

**Parameters:** ``$string``, ``$suffix``

**Example:**
::

    $string = 'JohnSnow';
    $withoutSuffix = Strings::removeSuffix($string, 'Snow');

**Result:** ``John``

----

startsWith
~~~~~~~~~~
Checks if string starts with ``$prefix``.

**Parameters:** ``$string``, ``$prefix``

**Example:**
::

    $string = 'prefixRest';
    $result = Strings::startsWith($string, 'prefix');

**Result:** ``true``

----

endsWith
~~~~~~~~
Checks if string ends with ``$suffix``.

**Parameters:** ``$string``, ``$suffix``

**Example:**
::

    $string = 'StringSuffix';
    $result = Strings::endsWith($string, 'Suffix');

**Result:** ``String``

----

equalsIgnoreCase
~~~~~~~~~~~~~~~~
Determines whether two strings contain the same data, ignoring the case of the letters in the strings.

**Parameters:** ``$string1``, ``$string2``

**Example:**
::

    $equal = Strings::equalsIgnoreCase('ABC123', 'abc123')

**Result:** ``true``

----

remove
~~~~~~
Removes all occurrences of a substring from string.

**Parameters:** ``$string``, ``$stringToRemove``

**Example:**
::

    $string = 'winter is coming???!!!';
    $result = Strings::remove($string, '???');

**Result:** ``winter is coming!!!``

----

appendSuffix
~~~~~~~~~~~~
Adds suffix to the string.

**Parameters:** ``$string``, ``$suffix = ''``

**Example:**
::

    $string = 'Daenerys';
    $stringWithSuffix = Strings::appendSuffix($string, ' Targaryen');

**Result:** ``Daenerys Targaryen``

appendPrefix
~~~~~~~~~~~~
Adds prefix to the string.

**Parameters:** ``$string``, ``$prefix = ''``

**Example:**
::

    $string = 'Targaryen';
    $stringWithPrefix = Strings::appendPrefix($string, 'Daenerys ');

**Result:** ``Daenerys Targaryen``

----

tableize
~~~~~~~~
Converts a word into the format for an Ouzo table name. Converts 'ModelName' to 'model_names'.

**Parameters:** ``$class``

**Example:**
::

    $class = "BigFoot";
    $table = Strings::tableize($class);

**Result:** ``BigFeet``

----

escapeNewLines
~~~~~~~~~~~~~~
Changes new lines to ``<br>`` and converts special characters to HTML entities.

**Parameters:** ``$string``

**Example:**
::

    $string = "My name is <strong>Reek</strong> \nit rhymes with leek";
    $escaped = Strings::escapeNewLines($string);

**Result:** ``My name is &lt;strong&gt;Reek&lt;/strong&gt; <br />\nit rhymes with leek``

----

htmlEntityDecode
~~~~~~~~~~~~~~~~
Alias for html_entity_decode() with UTF-8 and defined flag ENT_COMPAT.

**Parameters:** ``$text``

----

htmlEntities
~~~~~~~~~~~~
Alias for htmlentities() with UTF-8 and defined flag ENT_COMPAT.

**Parameters:** ``$text``

----

equal
~~~~~
Checks if string representations of two objects are equal.

**Parameters:** ``$object1``, ``$object2``

**Example:**
::

    $result = Strings::equal('0123', 123);

**Result:** ``false``

----

isBlank
~~~~~~~
Checks if string is blank.

**Parameters:** ``$string``

**Example:**
::

    $result = Strings::isBlank('0');

**Result:** ``false``

----

isNotBlank
~~~~~~~~~~
Checks if string is not blank.

**Parameters:** ``$string``

**Example:**
::

    $result = Strings::isNotBlank('0');

**Result:** ``true``

----

abbreviate
~~~~~~~~~~
Abbreviates a string using ellipsis.

**Parameters:** ``$string``, ``$maxWidth``

**Example:**
::

    $result = Strings::abbreviate('ouzo is great', 5);

**Result:** ``ouzo ...``

----

trimToNull
~~~~~~~~~~
Removes control characters from both ends of this string returning null if the string is empty ("") after the trim or if it is null.

**Parameters:** ``$string``

**Example:**
::

    $result = Strings::trimToNull('  ');

**Result:** ``null``

----

sprintfAssoc
~~~~~~~~~~~~
Replaces all occurrences of placeholder in string with values from associative array.

**Parameters:** ``$string``, ``$params``

**Example:**
::

    $sprintfString = "This is %{what}! %{what}? This is %{place}!";
    $assocArray = array(
      'what' => 'madness',
      'place' => 'Sparta'
    );

**Result:** ``This is madness! madness? This is Sparta!``

----

sprintAssocDefault
~~~~~~~~~~~~~~~~~~
Replaces all occurrences of placeholder in string with values from associative array.
When no value for placeholder is found in array, a default empty value is used if not otherwise specified.

**Parameters:** ``$string``, ``array $params``, ``$default = ''``

**Example:**
::

    $sprintfString = "This is %{what}! %{what}? This is %{place}!";
    $assocArray = array(
      'what' => 'madness',
      'place' => 'Sparta'
    );

**Result:** ``This is madness! madness? This is Sparta!``

----

contains
~~~~~~~~

Checks if string contains substring.

**Parameters:** ``$string``, ``$substring``

----

substringBefore
~~~~~~~~~~~~~~~
Gets the substring before the first occurrence of a separator. The separator is not returned.

If the separator is not found, the string input is returned.

**Parameters:** ``$string``, ``$separator``

**Example:**
::

    $string = 'winter is coming???!!!';
    $result = Strings::substringBefore($string, '?');

**Result:** ``winter is coming``

