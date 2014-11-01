<?php

use Ouzo\Controller;
use Ouzo\ControllerFactory;
use Ouzo\Extension\AuthBasicExtension;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;

class AuthSampleController extends Controller
{
    public function init()
    {
        AuthBasicExtension::register($this, array(
            'login' => 'login',
            'password' => 'pass'
        ));
    }

    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }
}

class AuthBasicControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerFactory = new ControllerFactory('\\');
        Route::$validate = false;
        Route::allowAll('/auth_sample', 'auth_sample');
    }

    public function tearDown()
    {
        Route::$validate = true;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldNotCallActionWhenNoCredentials()
    {
        //when
        CatchException::when($this)->get('/auth_sample/index');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\UnauthorizedException');
    }

    /**
     * @test
     */
    public function shouldNotCallActionWhenInvalidCredentials()
    {
        //given
        $_SERVER['PHP_AUTH_USER'] = 'login';
        $_SERVER['PHP_AUTH_PW'] = 'invalid';

        //when
        CatchException::when($this)->get('/auth_sample/index');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\UnauthorizedException');
    }

    /**
     * @test
     */
    public function shouldCallActionWhenValidCredentials()
    {
        //given
        $_SERVER['PHP_AUTH_USER'] = 'login';
        $_SERVER['PHP_AUTH_PW'] = 'pass';

        //when
        $this->get('/auth_sample/index');

        //then
        $this->assertRenderedContent()->isEqualTo('index');
    }
}