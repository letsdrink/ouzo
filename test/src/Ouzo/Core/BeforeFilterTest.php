<?php
use Ouzo\Controller;
use Ouzo\Routing\Route;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Utilities\Arrays;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public static $beforeActionResult;
    public static $actionCalled;
    public static $beforeCallback;

    public function __construct($routeRule)
    {
        parent::__construct($routeRule);
    }

    public function init()
    {
        $this->before[] = 'beforeAction';
        if (self::$beforeCallback) {
            $this->before[] = self::$beforeCallback;
        }
    }

    public function beforeAction()
    {
        return self::$beforeActionResult;
    }

    public function action()
    {
        self::$actionCalled = true;
    }
}

class MockControllerFactory
{
    public function createController()
    {
        $routeRule = Arrays::first(Route::getRoutes());
        return new SampleController($routeRule);
    }
}

class BeforeFilterTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerFactory = new MockControllerFactory();
        Route::$routes = array();
        SampleController::$beforeCallback = null;
    }

    /**
     * @test
     */
    public function shouldNotInvokeActionWhenBeforeFilterReturnsFalse()
    {
        //given
        SampleController::$beforeActionResult = false;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertFalse(SampleController::$actionCalled);
    }

    /**
     * @test
     */
    public function shouldInvokeActionWhenBeforeFilterReturnsTrue()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertTrue(SampleController::$actionCalled);
    }

    /**
     * @test
     */
    public function shouldInvokeFunctionCallback()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$beforeCallback = function ($controller) {
            $controller->redirect('url');
            return true;
        };

        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertRedirectsTo('url');
    }
}
