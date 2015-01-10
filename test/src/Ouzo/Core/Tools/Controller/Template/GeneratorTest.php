<?php
use Ouzo\Tools\Controller\Template\Generator;

class GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnControllerClassName()
    {
        //given
        $generator = new Generator('users');

        //when
        $className = $generator->getClassName();

        //then
        $this->assertEquals('UsersController', $className);
    }

    /**
     * @test
     */
    public function shouldReturnControllerClassNameWithControllerStringInName()
    {
        //given
        $generator = new Generator('users_controller');

        //when
        $className = $generator->getClassName();

        //then
        $this->assertEquals('UsersController', $className);
    }

    /**
     * @test
     */
    public function shouldReturnClassNamespace()
    {
        //given
        $generator = new Generator('users');

        //when
        $classNamespace = $generator->getClassNamespace();

        //then
        $this->assertEquals('\\Application\\Controller', $classNamespace);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfControllerNotExists()
    {
        //given
        $generator = new Generator('users');

        //when
        $isControllerExists = $generator->isControllerExists();

        //then
        $this->assertFalse($isControllerExists);
    }
}
