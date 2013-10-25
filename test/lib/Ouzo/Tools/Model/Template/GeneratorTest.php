<?php


namespace Ouzo\Tools\Model\Template;

use Ouzo\Config;
use Ouzo\Tests\Assert;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{

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
        $dialectReflectionClass = new \ReflectionClass($templateDialect);
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
        Assert::thatArray($dialectAdapter->columns())->onProperty('name')->containsOnly('id', 'id_category', 'name', 'description', 'sale');
        Assert::thatArray($dialectAdapter->columns())->onProperty('type')->contains('string', 'string', 'int', 'int');
    }
}
 