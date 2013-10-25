<?php


namespace Ouzo\Tools;


use Ouzo\Config;
use Ouzo\Tools\Model\Template\Dialect\PostgresDialect;
use Ouzo\Tools\Model\Template\Generator;

class PostgresDialectConfig
{
    public function getConfig()
    {
        $config['sql_dialect'] = "\\Ouzo\\Db\\Dialect\\PostgresDialect";
        return $config;
    }
}

class GeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldReturnObjectFroConfiguredDialect()
    {
        //given
        Config::registerConfig(new PostgresDialectConfig());
        $generator = new Generator('products');

        //when
        $templateDialect = $generator->dialectAdapter();

        //then
        $this->assertTrue($templateDialect instanceof PostgresDialect);
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
}
 