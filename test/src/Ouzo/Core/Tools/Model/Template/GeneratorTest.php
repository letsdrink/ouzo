<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template;

use Ouzo\Config;
use Ouzo\Db\Dialect\Dialect;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tools\Model\Template\Generator;
use Ouzo\Tools\Model\Template\GeneratorException;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $driver = Config::getValue('db', 'driver');
        if ($driver == 'sqlite') {
            $this->markTestSkipped('This test is not for SQLite database.');
        }
        parent::setUp();
    }

    /**
     * @test
     */
    public function shouldReturnObjectForConfiguredDialect()
    {
        //given
        $generator = new Generator('products');

        //when
        $templateDialect = $generator->dialectAdapter();

        //then
        $configuredDialectClassPath = Config::getValue('sql_dialect');
        $dialectReflectionClass = new ReflectionClass($templateDialect);
        $generatorDialectClassName = $dialectReflectionClass->getShortName();
        $this->assertStringEndsWith($generatorDialectClassName, $configuredDialectClassPath);
    }

    /**
     * @test
     */
    public function shouldRemoveTablePrefixFromClass()
    {
        //given
        $generator = new Generator('t_my_table');

        //when
        $modelName = $generator->getTemplateClassName();

        //then
        $this->assertEquals('MyTable', $modelName);
    }

    /**
     * @test
     */
    public function shouldSingularizeTableName()
    {
        //given
        $generator = new Generator('order_products');

        //when
        $modelName = $generator->getTemplateClassName();

        //then
        $this->assertEquals('OrderProduct', $modelName);
    }

    /**
     * @test
     */
    public function shouldReturnTableInformation()
    {
        //given
        $generator = new Generator('products');

        //when
        $dialectAdapter = $generator->dialectAdapter();

        //then
        $this->assertEquals('id', $dialectAdapter->primaryKey());
        Assert::thatArray($dialectAdapter->columns())->onProperty('name')->containsOnly('id', 'id_category', 'name', 'description', 'sale', 'id_manufacturer');
        Assert::thatArray($dialectAdapter->columns())->onProperty('type')->contains('string', 'string', 'int', 'int');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenDialectAdapterNotExists()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Tools\Model\Template\MyImagineDialect');

        //when
        try {
            new Generator('order_products');
            $this->fail();
        } catch (GeneratorException $e) {
        }

        //then
        Config::revertProperty('sql_dialect');
    }

    /**
     * @test
     */
    public function shouldSaveToFile()
    {
        //given
        $generator = new Generator('products');
        $fileName = '/tmp/example.php';

        //when
        $generator->saveToFile($fileName);

        //then
        $this->assertFileExists($fileName);
        unlink($fileName);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenFileAlreadyExists()
    {
        //given
        $generator = new Generator('products');
        $fileName = '/tmp/example.php';
        file_put_contents($fileName, '');

        //when
        CatchException::when($generator)->saveToFile($fileName);

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Tools\Model\Template\GeneratorException');
        unlink($fileName);
    }

    /**
     * @test
     */
    public function shouldPrepareTemplateContents()
    {
        //given
        $generator = new Generator('products');

        //when
        $template = $generator->templateContents();

        //then
        $this->assertContains('class Product extends Model', $template);
        $this->assertContains('string description', $template);
    }
}

/**
 * @SuppressWarnings(PHPMD)
 */
class MyImagineDialect extends Dialect
{
    public function getConnectionErrorCodes()
    {
        return array();
    }

    public function getErrorCode($errorInfo)
    {
        return 0;
    }
}
