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

Auto-wiring dependencies (with #[Inject]):

```php
class MyClass
{
  #[Inject]
  private \OtherClass $otherClass;
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
  #[Inject]
  #[Named('some_name')]
  private \OtherClass $otherClass;
}
```

For named binding in constructor use:

```php
class MyClass
{
    private $otherClass;
    private $andAnotherClass;

    #[Inject]
    #[Named('some_name', 'otherClass')]
    #[Named('new_named', 'andAnotherClass')]
    public function __construct(OtherClass $otherClass, AndAnotherClass $andAnotherClass)
    {
        $this->otherClass = $otherClass;
        $this->andAnotherClass = $andAnotherClass;
    }
}
```

You can named any constructor argument using parameter name.

Constructor injection (requires arguments types):

```php
class MyClass
{
    private $otherClass;

    #[Inject]
    public function __construct(OtherClass $otherClass)
    {
        $this->otherClass = $otherClass;
    }
}
```

By default all singletons will be lazy loaded IF lazy loading is enabled in configuration.
Enabling it with ProxyManager (https://github.com/Ocramius/ProxyManager) implementation:

```php
$lazyCreator = new ProxyManagerInstanceCreator(new Configuration())

$config = new InjectorConfig();
$config->setLazyInstanceCreator($lazyCreator);
```

If you want your singletons to be loaded eagerly use `asEagerSingleton` method:

```php
$config = new InjectorConfig();
$config->bind('\MyClass')->asEagerSingleton();
```

Multi bindings (injecting list of specified type, can be done via property or constructor):

```php
$config = new InjectorConfig();
$config->bind(OtherClass::class)->to(OtherClass1::class, OtherClass2::class);
```

```php
class MyClass
{
  #[InjectList(OtherClass::class)]
  private array $otherClass;
}
```

Multi bindings can be named:

```php
$config = new InjectorConfig();
$config->bind(OtherClass::class, 'some_name')->to(OtherClass1::class, OtherClass2::class);
```

```php
class MyClass
{
  #[InjectList(OtherClass::class, 'some_name')]
  private array $otherClass;
}
```
