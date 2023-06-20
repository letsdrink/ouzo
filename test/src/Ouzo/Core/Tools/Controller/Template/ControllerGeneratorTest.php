<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tools\Controller\Template\ActionGenerator;
use Ouzo\Tools\Controller\Template\ControllerGenerator;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ControllerGeneratorTest extends TestCase
{
    private $controllerPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllerPath = Path::joinWithTemp('UsersController.php');
    }

    protected function tearDown(): void
    {
        if (Files::exists($this->controllerPath)) {
            Files::delete($this->controllerPath);
        }
        parent::tearDown();
    }

    #[Test]
    public function shouldReturnControllerClassName()
    {
        //given
        $generator = new ControllerGenerator('users');

        //when
        $className = $generator->getClassName();

        //then
        $this->assertEquals('UsersController', $className);
    }

    #[Test]
    public function shouldReturnControllerClassNameWithControllerStringInName()
    {
        //given
        $generator = new ControllerGenerator('users_controller');

        //when
        $className = $generator->getClassName();

        //then
        $this->assertEquals('UsersController', $className);
    }

    #[Test]
    public function shouldReturnClassNamespace()
    {
        //given
        $generator = new ControllerGenerator('users');

        //when
        $classNamespace = $generator->getClassNamespace();

        //then
        $this->assertEquals('\\Application\\Controller', $classNamespace);
    }

    #[Test]
    public function shouldReturnFalseIfControllerNotExists()
    {
        //given
        $generator = new ControllerGenerator('users');

        //when
        $isControllerExists = $generator->isControllerExists();

        //then
        $this->assertFalse($isControllerExists);
    }

    #[Test]
    public function shouldAppendAction()
    {
        //given
        $controllerGenerator = new ControllerGenerator('users', $this->controllerPath);
        $controllerGenerator->saveController();

        //when
        $appendAction = $controllerGenerator->appendAction(new ActionGenerator('index'));

        //then
        $this->assertTrue($appendAction);
        Assert::thatString($controllerGenerator->getControllerContents())
            ->contains('class UsersController extends Controller')
            ->contains('public function index()');
    }

    #[Test]
    public function shouldAppendActionWhenControllerHasActions()
    {
        //given
        $controllerStub = '<?php
namespace \Application\Controller;

use Ouzo\Controller;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class UsersController extends Controller
{
    public function index()
    {
        echo "some actions";
        $this->view->render();
    }
}';
        file_put_contents($this->controllerPath, $controllerStub);
        $controllerGenerator = new ControllerGenerator('users', $this->controllerPath);

        //when
        $appendAction = $controllerGenerator->appendAction(new ActionGenerator('save'));

        //then
        $this->assertTrue($appendAction);
        Assert::thatString($controllerGenerator->getControllerContents())
            ->contains('public function index()')
            ->contains('echo "some actions";')
            ->contains('public function save()');
    }

    #[Test]
    public function shouldNotAppendWhenActionIsExists()
    {
        //given
        $controllerGenerator = new ControllerGenerator('users', $this->controllerPath);
        $controllerGenerator->saveController();
        $controllerGenerator->appendAction(new ActionGenerator('save'));

        //when
        $appendAction = $controllerGenerator->appendAction(new ActionGenerator('save'));

        //then
        $this->assertFalse($appendAction);
    }
}
