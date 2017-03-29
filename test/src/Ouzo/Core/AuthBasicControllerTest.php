<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Extension\AuthBasicExtension;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;

class AuthSampleController extends Controller
{
    public function init()
    {
        AuthBasicExtension::register($this, [
            'login' => 'login',
            'password' => 'pass'
        ]);
    }

    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }
}

class AuthBasicControllerTest extends ControllerTestCase
{
    public function __construct()
    {
        Config::overrideProperty('namespace', 'controller')->with('\\');
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        Route::$validate = false;
        Route::allowAll('/auth_sample', 'auth_sample');
    }

    public function tearDown()
    {
        Route::$validate = true;
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
    }

    /**
     * @test
     */
    public function shouldNotCallActionWhenNoCredentials()
    {
        //when
        CatchException::when($this)->get('/auth_sample/index');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Exception\UnauthorizedException');
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
        CatchException::assertThat()->isInstanceOf('\Ouzo\Exception\UnauthorizedException');
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
