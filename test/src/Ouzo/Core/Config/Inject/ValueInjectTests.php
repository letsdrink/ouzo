<?php

use Ouzo\Config;
use Ouzo\Config\Inject\Value;
use Ouzo\Config\Inject\ValueCustomAttributeInject;
use Ouzo\Injection\Annotation\Custom\CustomAttributeInjectRegistry;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Injector;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;

class ValueInjectSampleConfig
{
    public function getConfig(): array
    {
        return ['properties' =>
            [
                'field' => 'property to filed',
                'constructor_parameter' => 999,
            ]
        ];
    }
}

class SampleClass
{
    #[Value('${properties.field}')]
    private string $field;

    #[Value('properties.field')]
    private string $exactValue;

    #[Inject]
    public function __construct(
        #[Value('${properties.constructor_parameter}')] private int $constructorParameter,
        private InjectedClass $injectedClass
    )
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getConstructorParameter(): int
    {
        return $this->constructorParameter;
    }

    public function getInjectedClass(): InjectedClass
    {
        return $this->injectedClass;
    }

    public function getExactValue(): string
    {
        return $this->exactValue;
    }
}

class InjectedClass
{
}

class ValueInjectTests extends TestCase
{
    private Injector $injector;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Config::registerConfig(new ValueInjectSampleConfig())->reload();
    }

    public function setUp(): void
    {
        parent::setUp();

        $customAttributeInjectRegistry = new CustomAttributeInjectRegistry();
        $customAttributeInjectRegistry->register(new ValueCustomAttributeInject());
        $this->injector = new Injector(customAttributeInjectRegistry: $customAttributeInjectRegistry);
    }

    /**
     * @test
     */
    public function shouldInjectValueIntoProperty()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $field = $sampleClass->getField();

        //then
        $this->assertEquals('property to filed', $field);
    }

    /**
     * @test
     */
    public function shouldInjectValueIntoConstructorArgument()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $constructorParameter = $sampleClass->getConstructorParameter();

        //then
        $this->assertEquals(999, $constructorParameter);
    }

    /**
     * @test
     */
    public function shouldInjectValueIntoConstructorArgumentWithAnotherClass()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $constructorParameter = $sampleClass->getConstructorParameter();

        //then
        $this->assertEquals(999, $constructorParameter);
        $this->assertInstanceOf(InjectedClass::class, $sampleClass->getInjectedClass());
    }

    /**
     * @test
     */
    public function shouldInjectExactValueInto()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $exactValue = $sampleClass->getExactValue();

        //then
        $this->assertEquals('properties.field', $exactValue);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTryToInjectMismatchedParameterType()
    {
        //given
        Config::overrideProperty('properties', 'constructor_parameter')->with('invalid int value');

        //when
        CatchException::when($this->injector)->getInstance(SampleClass::class);

        //then
        CatchException::assertThat()
            ->isInstanceOf(TypeError::class)
            ->hasMessage('SampleClass::__construct(): Argument #1 ($constructorParameter) must be of type int, string given');
    }
}
