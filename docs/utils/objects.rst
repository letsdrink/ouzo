Objects
=======

Helper functions that can operate on any php Object.

toString
~~~~~~~~
Returns a string representation of the given object.
If the given object implements ``__toString`` method it will be used.

**Parameters:** ``$var``

**Example:**
::

    Objects::toString('string'); //=> "string"
    Objects::toString(null); //=> null
    Objects::toString(1); //=> 1
    Objects::toString(true); //=> true

    Objects::toString(array('a', 1)); //=> ["a", 1]

    Objects::toString(array('key' => 'value1', 'key2' => 'value2'));
    //=> [<key> => "value1", <key2> => "value2"]

    $object = new stdClass();
    $object->field1 = 'field1';
    $object->field2 = 'field2';

    Objects::toString($object);
    //=> stdClass {<field1> => "field1", <field2> => "field2"}

getValue
~~~~~~~~
Returns value of a field or default if the field does not exist or is null.

**Parameters:** ``$object``, ``$field``, ``$default = null``

**Example:**
::

    $object = new stdClass();
    $object->field1 = 'value';

    $result = Objects::getValue($object, 'field1');

Returns: ``'value'``

::

    $object = new stdClass();

    $result = Objects::getValue($object, 'field1', 'not found');

Returns: ``'not found'``

setValueRecursively
~~~~~~~~~~~~~~~~~~~
Sets value of a nested field.
 
**Parameters:** ``$object``, ``$names``, ``$value``

**Example:**
::

    $object = new stdClass();
    $object->field1 = new stdClass();
    Objects::setValueRecursively($object, 'field1->field2', 'value')

    echo $object->field1->field2

will echo ``'value'``.

getValueRecursively
~~~~~~~~~~~~~~~~~~~
Returns value of a nested field or default if the field does not exist.

The ``$names`` parameter can also contain method calls e.g.:
``'field->method()->field'``

**Parameters:** ``$object``, ``$names``, ``$default = null``

**Example:**
::

    $object = new stdClass();
    $object->field1 = new stdClass();
    $object->field1->field2 = 'value';

    $result = Objects::getValueRecursively($object, 'field1->field2');

**Result:** ``'value'``

**Example2:**
::

    $object = new stdClass();
    $object->field1 = new stdClass();

    $result = Objects::getValueRecursively($object, 'field1->field2->field3', 'not found');

**Result:** ``'not found'``
