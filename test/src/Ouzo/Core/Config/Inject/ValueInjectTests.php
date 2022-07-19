<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Config\Inject\Value;
use Ouzo\Config\Inject\ValueAttributeInjector;
use Ouzo\Injection\Annotation\AttributeInjectorRegistry;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Tests\Assert;
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
                'errors' => ['error1', 'error2', 'error3']
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

    #[Value('${properties.errors}')]
    private array $errors;

    #[Value('${properties.invalid:test}')]
    private string $fieldWithDefaultString;

    #[Value('${properties.invalid:}')]
    private string $fieldWithDefaultEmpty;

    #[Value('${properties.invalid:123}')]
    private int $fieldWithDefaultInt;

    #[Inject]
    public function __construct(
        #[Value('${properties.constructor_parameter}')] private int $constructorParameter,
        private InjectedClass $injectedClass,
        private InjectedClassWithConfig $injectedClassWithConfig
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

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getInjectedClassWithConfig(): InjectedClassWithConfig
    {
        return $this->injectedClassWithConfig;
    }

    public function getFieldWithDefaultString(): string
    {
        return $this->fieldWithDefaultString;
    }

    public function getFieldWithDefaultEmpty(): string
    {
        return $this->fieldWithDefaultEmpty;
    }

    public function getFieldWithDefaultInt(): int
    {
        return $this->fieldWithDefaultInt;
    }
}

class InjectedClassWithConfig
{
    #[Value('${properties.field}')]
    private string $field;

    #[Value('${properties.errors}')]
    private array $errors;

    public function getField(): string
    {
        return $this->field;
    }

    public function getErrors(): array
    {
        return $this->errors;
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

        $attributeInjectorRegistry = new AttributeInjectorRegistry();
        $attributeInjectorRegistry->register(new ValueAttributeInjector());
        $this->injector = new Injector(new InjectorConfig(), $attributeInjectorRegistry);
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
        Config::revertProperty('properties', 'constructor_parameter');
    }

    /**
     * @test
     */
    public function shouldInjectConfigValueWhichIsArray()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $errors = $sampleClass->getErrors();

        //then
        Assert::thatArray($errors)->containsOnly('error1', 'error2', 'error3');
    }

    /**
     * @test
     */
    public function shouldInjectClassWithValuesAsProperties()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $injectedClassWithConfig = $sampleClass->getInjectedClassWithConfig();

        //then
        $this->assertEquals('property to filed', $injectedClassWithConfig->getField());
        Assert::thatArray($injectedClassWithConfig->getErrors())->containsOnly('error1', 'error2', 'error3');
    }

    /**
     * @test
     */
    public function shouldInjectDefaultStringValue()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $value = $sampleClass->getFieldWithDefaultString();

        //then
        $this->assertEquals('test', $value);
    }

    /**
     * @test
     */
    public function shouldInjectDefaultIntValue()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $value = $sampleClass->getFieldWithDefaultInt();

        //then
        $this->assertEquals(123, $value);
    }

    /**
     * @test
     */
    public function shouldInjectDefaultEmptyValue()
    {
        //given
        /** @var SampleClass $sampleClass */
        $sampleClass = $this->injector->getInstance(SampleClass::class);

        //when
        $value = $sampleClass->getFieldWithDefaultEmpty();

        //then
        $this->assertEmpty($value);
    }
}
