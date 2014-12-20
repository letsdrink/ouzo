## underscoreToCamelCase
Changes underscored string to the camel case.

**Parameters:** `$string`

**Example:**
```php
$string = 'lannisters_always_pay_their_debts';
$camelcase = Strings::underscoreToCamelCase($string);
```

**Result:** `LannistersAlwaysPayTheirDebts`

## camelCaseToUnderscore
Changes camel case string to underscored.

**Parameters:** `$string`

**Example:**
```php
$string = 'LannistersAlwaysPayTheirDebts';
$underscored = Strings::camelCaseToUnderscore($string);
```

**Result:** `lannisters_always_pay_their_debts`

## removePrefix
Returns a new string without the given prefix.

**Parameters:** `$string`, `$prefix`

**Example:**
```php
$string = 'prefixRest';
$withoutPrefix = Strings::removePrefix($string, 'prefix');
```

**Result:** `Rest`

## removePrefixes
Removes prefixes defined in array from string.

**Parameters:** `$string`, `array $prefixes`

**Example:**
```php
$string = 'prefixRest';
$withoutPrefix = Strings::removePrefixes($string, array('pre', 'fix'));
```

**Result:** `Rest`

## startsWith
Method checks if string starts with `$prefix`.

**Parameters:** `$string`, `$prefix`

**Example:**
```php
$string = 'prefixRest';
$result = Strings::startsWith($string, 'prefix');
```

**Result:** `true`

## endsWith
Method checks if string ends with `$suffix`.

**Parameters:** `$string`, `$suffix`

**Example:**
```php
$string = 'StringSuffix';
$result = Strings::endsWith($string, 'Suffix');
```

**Result:** `String`

## equalsIgnoreCase
Determines whether two strings contain the same data, ignoring the case of the letters in the strings.

**Parameters:** `$string1`, `$string2`

**Example:**
```php
$equal = Strings::equalsIgnoreCase('ABC123', 'abc123')
```

**Result:** `true`

## remove
Removes all occurrences of a substring from string.

**Parameters:** `$string`, `$stringToRemove`

**Example:**
```php
$string = 'winter is coming???!!!';
$result = Strings::remove($string, '???');
```

**Result:** `winter is coming!!!`

## appendSuffix
Adds suffix to the string.

**Parameters:** `$string`, `$suffix = ''`

**Example:**
```php
$string = 'Daenerys';
$stringWithSuffix = Strings::appendSuffix($string, ' Targaryen');
```

**Result:** `Daenerys Targaryen`

## tableize
Converts a word into the format for an Ouzo table name. Converts 'ModelName' to 'model_names'.

**Parameters:** `$class`

**Example:**
```php
$class = "BigFoot";
$table = Strings::tableize($class);
```

**Result:** `BigFeet`

## escapeNewLines
Changes new lines to `<br>` and converts special characters to HTML entities.

**Parameters:** `$string`

**Example:**
```php
$string = "My name is <strong>Reek</strong> \nit rhymes with leek";
$escaped = Strings::escapeNewLines($string);
```

**Result:** `My name is &lt;strong&gt;Reek&lt;/strong&gt; <br />\nit rhymes with leek`


## trimToNull
Removes control characters from both ends of this string returning null if the string is empty ("") after the trim or if it is null.

**Parameters:** `$string`

**Example:**
```php
$result = Strings::trimToNull('  ');
```

**Result:** `null`