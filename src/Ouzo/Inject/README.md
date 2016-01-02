Ouzo Inject
==============

This is experimental Dependency Injection framework for Ouzo.

Usage:

```php
$injector = new Injector();
$injector->getInstance('\MyClass');
```

Scope definition:

```php
$config = new InjectorConfig();
$config->bind('\MyClass')->in(Scope::SINGLETON);

$injector = new Injector($config);
$injector->getInstance('\MyClass');
```

Auto-wiring dependencies (with @Inject):

```php
class MyClass
{
  /**
   * @Inject
   * @var \OtherClass
   */
  private $otherClass;
}
```