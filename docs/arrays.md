Helper functions that can operate on arrays.

## all
Returns true if every element in array satisfies the predicate.

**Parameters:** `array $elements`, `$predicate`

**Example:**
```php
$array = array(1, 2);
$all = Arrays::all($array, function ($element) {
	return $element < 3;
});
```

**Result:** `true`

## toMap
This method creates associative array using key and value functions on array elements.

**Parameters:** `array $elements`, `$keyFunction`, `$valueFunction = null`

**Example:**
```php
$array = range(1, 2);
$map = Arrays::toMap($array, function ($elem) {
	return $elem * 10;
}, function ($elem) {
	return $elem + 1;
}); 
```

**Result:**
```
Array
(
    [10] => 2
    [20] => 3
)
```

## flatten
Returns a new array that is a one-dimensional flattening of the given array.

**Parameters:** `array $elements`

**Example:**
```php
$array = array(
	'names' => array(
		'john',
		'peter',
		'bill'
	),
	'products' => array(
		'cheese',
		array('milk', 'brie')
	)
);
$flatten = Arrays::flatten($array);
```

**Result:**
```
Array
(
    [0] => john
    [1] => peter
    [2] => bill
    [3] => cheese
    [4] => milk
    [5] => brie
)
```

## findKeyByValue
This method returns a key for the given value.

**Parameters:** `array $elements`, `$value`

**Example:**
```php
$array = array(
	'k1' => 4,
	'k2' => 'd',
	'k3' => 0,
	9 => 'p'
);
$key = Arrays::findKeyByValue($array, 0);
```

**Result:** `k3`

## any
Returns true if at least one element in the array satisfies the predicate.

**Parameters:** `array $elements`, `$predicate`

**Example:**
```php
$array = array('a', true, 'c');
$any = Arrays::any($array, function ($element) {
	return is_bool($element);
});
```

**Result:** `true`

## first
This method returns the first value in the given array .

**Parameters:** `array $elements`

**Example:**
```php
$array = array('one', 'two' 'three');
$first = Arrays::first($array);
```

**Result:** `one`

## last
This method returns the last value in the given array.

**Parameters:** `array $elements`

**Example:**
```php
$array = array('a', 'b', 'c');
$last = Arrays::last($array);
```

**Result:** `c`

## firstOrNull
This method returns the first value or `null` if array is empty.

**Parameters:** `array $elements`

**Example:**
```php
$array = array();
$return = Arrays::firstOrNull($array);
```

**Result:** `null`

## getValue
Returns the element for the given key or a default value otherwise.

**Parameters:** `array $elements`, `$key`, `$default = null`

**Example:**
```php
$array = array('id' => 1, 'name' => 'john');
$value = Arrays::getValue($array, 'name');
```

**Result:** `john`

**Example:**
```php
$array = array('id' => 1, 'name' => 'john');
$value = Arrays::getValue($array, 'surname', '--not found--');
```

**Result:** `--not found--`

## filterByAllowedKeys
Returns an array containing only the given keys. 
**Example:**
```php
$array = array('a' => 1, 'b' => 2, 'c' => 3);
$filtered = Arrays::filterByAllowedKeys($array, array('a', 'b'));
```

**Result:** 
```
Array
(
    [a] => 1
    [b] => 2
)
```

## filterByKeys
Filters array by keys using the predicate.

**Example:**
```php
$array = array('a1' => 1, 'a2' => 2, 'c' => 3);
$filtered = Arrays::filterByKeys($array, function ($elem) {
	return $elem[0] == 'a';
});
```

**Result:** 
```
Array
(
    [a1] => 1
    [b2] => 2
)
```

## groupBy
Group elements in array using function to grouping elements. If set `$orderField` grouped elements will be also sorted.

**Parameters:** `array $elements`, `$keyFunction`, `$orderField = null`

**Example:**
```php
$obj1 = new stdClass();
$obj1->name = 'a';
$obj1->description = '1';

$obj2 = new stdClass();
$obj2->name = 'b';
$obj2->description = '2';

$obj3 = new stdClass();
$obj3->name = 'b';
$obj3->description = '3';

$array = array($obj1, $obj2, $obj3);
$grouped = Arrays::groupBy($array, Functions::extractField('name'));
```

**Result:**
```
Array
(
    [a] => Array
        (
            [0] => stdClass Object
                (
                    [name] => a
                    [description] => 1
                )

        )

    [b] => Array
        (
            [0] => stdClass Object
                (
                    [name] => b
                    [description] => 2
                )

            [1] => stdClass Object
                (
                    [name] => b
                    [description] => 3
                )

        )

)
```

## orderBy
This method sorts elements in array using order field.

**Parameters:** `array $elements`, `$orderField`

**Example:**
```php
$obj1 = new stdClass();
$obj1->name = 'a';
$obj1->description = '1';

$obj2 = new stdClass();
$obj2->name = 'c';
$obj2->description = '2';

$obj3 = new stdClass();
$obj3->name = 'b';
$obj3->description = '3';

$array = array($obj1, $obj2, $obj3);
$sorted = Arrays::orderBy($array, 'name');
```

**Result:**
```
Array
(
    [0] => stdClass Object
        (
            [name] => a
            [description] => 1
        )

    [1] => stdClass Object
        (
            [name] => b
            [description] => 3
        )

    [2] => stdClass Object
        (
            [name] => c
            [description] => 2
        )

)
```
## sort
Returns a new array with is sorted using given comparator.

The comparator function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.

To obtain comparator one may use `Comparator` class (for instance `Comparator::natural()` which yields ordering using comparison operators).

**Parameters:** `array $array`, `$comparator`

**Example:**
```php
 class Foo
 {
      private $value;
      function __construct($value)
      {
          $this->value = $value;
      }
      public function getValue()
      {
          return $this->value;
      }
 }
$values = array(new Foo(1), new Foo(3), new Foo(2));
$sorted = Arrays::sort($values, Comparator::compareBy('getValue()'));
```

**Result:**
```
Array
(
     [0] =>  class Foo (1) {
                 private $value => int(1)
             }
     [1] =>  class Foo (1) {
                 private $value => int(2)
             }
     [2] =>  class Foo (1) {
                 private $value => int(3)
             }
)
```

## mapKeys
This method maps array keys using the function.

Invokes the function for each key in the array. 
Creates a new array containing the keys returned by the function.

**Parameters:** `array $elements`, `$function`

**Example:**
```php
$array = array(
	'k1' => 'v1',
	'k2' => 'v2',
	'k3' => 'v3',
);
$arrayWithNewKeys = Arrays::mapKeys($array, function ($key) {
	return 'new_' . $key;
});
```

**Result:**
```
Array
(
    [new_k1] => v1
    [new_k2] => v2
    [new_k3] => v3
)
```

## map
This method maps array values using the function.

Invokes the function for each value in the array. 
Creates a new array containing the values returned by the function.

**Parameters:** `array $elements`, `$function`

**Example:**
```php
$array = array('k1', 'k2', 'k3');
$result = Arrays::map($array, function ($value) {
	return 'new_' . $value;
});
```

**Result:**
```
Array
(
    [0] => new_k1
    [1] => new_k2
    [2] => new_k3
)
```

## filter
This method filters array using function. Result contains all elements for which function  returns `true`
**Parameters:** `$elements`, `$function`

**Example:**
```php
$array = array(1, 2, 3, 4);
$result = Arrays::filter($array, function ($value) {
	return $value > 2;
});
```

**Result:**
```
Array
(
    [2] => 3
    [3] => 4
)
```

## toArray
Make array from element. Returns the given argument if it's already an array.

**Parameters:** `$element`

**Example:**
```php
$result = Arrays::toArray('test');
```

**Result:**
```
Array
(
    [0] => test
)
```

## randElement
Returns a random element from the given array.

**Parameters:** `array $elements`

**Example:**
```php
$array = array('john', 'city', 'small');
$rand = Arrays::randElement($array);
```

**Result:** _rand element from array_

## combine
Returns a new array with `$keys` as array keys and `$values` as array values.

**Parameters:** `array $keys`, `array $values`

**Example:**
```php
$keys = array('id', 'name', 'surname');
$values = array(1, 'john', 'smith');
$combined = Arrays::combine($keys, $values);
```

**Result:**
```
Array
(
    [id] => 1
    [name] => john
    [surname] => smith
)
```

## keyExists
Checks is key exists in an array.

**Parameters:** `array $elements`, `$key`

**Example:**
```php
$array = array('id' => 1, 'name' => 'john');
$return = Arrays::keyExists($array, 'name');
```

**Result:** `true`

## count
Returns the number of elements for which the predicate returns true.

**Parameters:** `array $elements`, `$predicate`

**Example:**
```php
$array = array(1, 2, 3);
$count = Arrays::count($array, function ($element) {
   return $element < 3;
});
```

**Result:** 2

## Handling nested keys
There is a bunch of methods that helps whenever array with nested keys is in place.

### getNestedValue
```php
$array = ['1' => ['2' => ['3' => 'value']]];
Arrays::getNestedValue($array, ['1', '2', '3']);
```
**Result:** `value`

### setNestedValue
```php
$array = [];
Arrays::setNestedValue($array, ['1', '2', '3'], 'value');
```

### hasNestedValue
Deprecated. Use **hasNestedKey**.

### hasNestedKey
```php
$array = ['1' => ['2' => ['3' => 'value']]];
Arrays::hasNestedKey($array, ['1', '2']);
```
**Result:** `true`

### removeNestedValue
Deprecated. Use **removeNestedKey**.

### removeNestedKey
```php
$array = ['1' => ['2' => ['3' => 'value']]];
Arrays::removeNestedKey($array, ['1', '2']);
```