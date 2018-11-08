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

Default scope is Scope::PROTOTYPE.

Linked binding:

```php
$config = new InjectorConfig();
$config->bind('\MyClass')->to('\MySubClass');
```

Instance binding:

```php
$config = new InjectorConfig();
$config->bind('\MyClass')->toInstance(new MyClass());
```

Binding through factory class (`Factory` interface has to be implemented):

```php
$config = new InjectorConfig();
$config->bind('\MyClass')->throughFactory('\MyClassFactory');
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

Named binding:

```php
$config = new InjectorConfig();
$config->bind('\MyClass', 'some_name')->in(Scope:SINGLETON);
```

Auto-wiring for named binding:

```php
class MyClass
{
  /**
   * @Inject @Named("some_name")
   * @var \OtherClass
   */
  private $otherClass;
}
```

Constructor injection (requires arguments types):

```php
class MyClass
{
    private $otherClass;

    /**
     * @Inject
     */
    public function __construct(OtherClass $otherClass)
    {
        $this->otherClass = $otherClass;
    }
}
```
