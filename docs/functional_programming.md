Let's assume that you have a User class that has a list of addresses. Each address has a type (like: home, invoice etc.) and User has getAddress($type) method.

Now, let's write a code that given a list of users, returns a lists of unique non-empty cities from users' home addresses.


Pure php:
```php
$cities = array_unique(array_filter(array_map(function($user) {
   $address = $user>getAddress('home');
   return $address? $address->city : null;
}, $users)));
```

Ouzo:
```php
$cities = FluentArray::from($users)
             ->map(Functions::extract()->getAddress('home')->city)
             ->filter(Functions::notEmpty())
             ->unique()
             ->toArray();
```

[FluentArray](fluent_array.md)

[Functions::extract](functions.md#extract)


### Composing functions

Class FluentFunctions allows you to easily compose functions from Functions.

```php
$usersWithSurnameStartingWithB = 
      Arrays::filter($users, FluentFunctions::extractField('surname')->startsWith('B'));
```

is equivalent of:

```php
$usersWithSurnameStartingWithB = Arrays::filter($users, function($user) {
    $extractField = Functions::extractField('name');
    $startsWith = Functions::startsWith('B');
    return $startsWith($extractField($product));
});
```


Another example:
```php
$bobs = Arrays::filter($users, FluentFunctions::extractField('name')->equals('Bob'));
```

[FluentFunctions](fluent_functions.md)
