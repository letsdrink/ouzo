<?php
namespace Ouzo\Tools\Model\Template;

use Ouzo\Config;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Path;
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
        Assert::thatArray($dialectAdapter->columns())->onProperty('name')->containsOnly('id', 'id_category', 'name', 'description', 'sale');
        Assert::thatArray($dialectAdapter->columns())->onProperty('type')->contains('string', 'string', 'int', 'int');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenDialectAdapterNotExists()
    {
        //given
        $old = Config::getValue('sql_dialect');
        $dialectPath = Path::join(ROOT_PATH, 'src', 'Ouzo', 'Db', 'Dialect', 'MyImagineDialect.php');
        file_put_contents($dialectPath, '<?php namespace Ouzo\Db\Dialect; class MyImagineDialect extends Dialect { }');
        Config::overrideProperty('sql_dialect')->with('\\Ouzo\\Db\\Dialect\\MyImagineDialect');

        //when
        try {
            new Generator('order_products');
        } catch (GeneratorException $e) {

        }

        //then
        unlink($dialectPath);
        Config::overrideProperty('sql_dialect')->with($old);
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
        $this->assertTrue(file_exists($fileName));
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
        $this->assertContains("'table' => 'products'", $template);
    }
}