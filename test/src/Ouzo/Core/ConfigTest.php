<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Files;
use PHPUnit\Framework\TestCase;

class SampleConfig
{
    /** @var array */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getConfig(): array
    {
        return ['default' => $this->values];
    }
}

class NoGetConfigMethod
{
}

class PrivateGetConfigMethod
{
    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function getConfig()
    {
        return [];
    }
}

class ConfigTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_SESSION['config']['debug'] = false;
        $_SESSION['config']['language'] = 'pl';
        $_SESSION['config']['custom'] = 'value';
        $_SESSION['config']['global']['prefix_system'] = '/sample';
    }

    public static function tearDownAfterClass(): void
    {
        Config::overrideProperty('debug')->with(true);
        Config::overrideProperty('language')->with('en');
        Config::overridePropertyArray(['global', 'prefix_system'], '');
    }

    /**
     * @test
     */
    public function shouldReturnNullForMissingSections()
    {
        // when
        $section = Config::getValue('missing');

        // then
        $this->assertNull($section);
    }

    /**
     * @test
     */
    public function shouldReadSampleConfig()
    {
        // given
        Config::registerConfig(new SampleConfig(['foo' => 'bar']))->reload();

        // when
        $value = Config::getValue('default');

        // then
        $this->assertEquals('bar', $value['foo']);
    }

    /**
     * @test
     */
    public function shouldReturnConfigValue()
    {
        // given
        Config::registerConfig(new SampleConfig(['cat' => 'dog']))->reload();

        // when
        $value = Config::getValue('default');

        // then
        $this->assertEquals('dog', $value['cat']);
    }

    /**
     * @test
     */
    public function shouldReturnNestedConfigValue()
    {
        // given
        Config::registerConfig(new SampleConfig(['frodo' => 'bilbo']))->reload();

        // when
        $value = Config::getValue('default', 'frodo');

        // then
        $this->assertEquals('bilbo', $value);
    }

    /**
     * @test
     */
    public function shouldReadMultipleSampleConfigs()
    {
        // given
        Config::registerConfig(new SampleConfig(['lorem' => 'ipsum']))->reload();
        Config::registerConfig(new SampleConfig(['dolor' => 'emet']))->reload();

        // when
        $value = Config::getValue('default');

        // then
        $this->assertEquals('ipsum', $value['lorem']);
        $this->assertEquals('emet', $value['dolor']);
    }

    /**
     * @test
     */
    public function shouldOverrideProperty()
    {
        // given
        Config::overrideProperty('key')->with('value');

        // when
        $value = Config::getValue('key');

        // then
        $this->assertEquals('value', $value);
        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     */
    public function shouldOverrideMultidimensionalProperty()
    {
        // given
        Config::overrideProperty('key1', 'key2')->with('value');

        // when
        $value = Config::getValue('key1', 'key2');

        // then
        $this->assertEquals('value', $value);
        Config::clearProperty('key1', 'key2'); // cleanup
    }

    /**
     * @test
     */
    public function shouldRevertProperty()
    {
        // given
        Config::overrideProperty('key1', 'key2')->with('first');
        Config::overrideProperty('key1', 'key2')->with('second');

        // when
        Config::revertProperty('key1', 'key2');

        // then
        $value = Config::getValue('key1', 'key2');
        $this->assertEquals('first', $value);

        Config::clearProperty('key1', 'key2'); // cleanup
    }

    /**
     * @test
     */
    public function revertCalledSecondTimeShouldDoNothingMore()
    {
        // given
        Config::overrideProperty('key')->with('first');
        Config::overrideProperty('key')->with('second');
        Config::revertProperty('key');

        // when
        Config::revertProperty('key');

        // then
        $value = Config::getValue('key');
        $this->assertEquals('first', $value);

        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     */
    public function revertOnNonExistingKeyShouldThrowException()
    {
        // then
        $this->expectException(InvalidArgumentException::class);

        // when
        Config::revertProperty('key', 'does', 'not', 'exist');
    }

    /**
     * @test
     */
    public function shouldClearProperty()
    {
        // given
        Config::overrideProperty('key_to_clear')->with('value');

        // when
        Config::clearProperty('key_to_clear');

        // then
        $value = Config::getValue('key_to_clear');
        $this->assertEmpty($value);
    }

    /**
     * @test
     */
    public function shouldReturnAllConfigValues()
    {
        // given
        Config::overrideProperty('key')->with('value');

        // when
        $values = Config::all();

        // then
        $this->assertEquals('value', $values['key']);
        Config::clearProperty('key'); // cleanup
    }

    /**
     * @test
     */
    public function shouldOverrideConfigPropertyBySession()
    {
        // when
        $values = Config::all();

        // then
        Assert::thatArray($values)->containsKeyAndValue(['debug' => false, 'language' => 'pl', 'custom' => 'value']);
        Assert::thatArray($values['global'])->contains('/sample');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenConfigMethodIsNotAnObject()
    {
        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Custom config must be a object');

        // when
        Config::registerConfig('config');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTryToAddCustomConfigWithoutGetConfigMethod()
    {
        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Custom config object must have getConfig method');

        // when
        Config::registerConfig(new NoGetConfigMethod());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTryToAddCustomConfigWhenGetConfigMethodIsNotPublic()
    {
        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Custom config method getConfig must be public');

        // when
        Config::registerConfig(new PrivateGetConfigMethod());
    }
}
