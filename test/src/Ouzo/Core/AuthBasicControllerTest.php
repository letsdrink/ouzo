<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Controller;
use Ouzo\Exception\UnauthorizedException;
use Ouzo\Extension\AuthBasicExtension;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

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
    public function setUp(): void
    {
        parent::setUp();
        Route::$validate = false;
        Route::allowAll('/auth_sample', AuthSampleController::class);
    }

    public function tearDown(): void
    {
        Route::$validate = true;
        parent::tearDown();
    }

    #[Test]
    public function shouldNotCallActionWhenNoCredentials()
    {
        //when
        CatchException::when($this)->get('/auth_sample/index');

        //then
        CatchException::assertThat()->isInstanceOf(UnauthorizedException::class);
    }

    #[Test]
    public function shouldNotCallActionWhenInvalidCredentials()
    {
        //given
        $_SERVER['PHP_AUTH_USER'] = 'login';
        $_SERVER['PHP_AUTH_PW'] = 'invalid';

        //when
        CatchException::when($this)->get('/auth_sample/index');

        //then
        CatchException::assertThat()->isInstanceOf(UnauthorizedException::class);
    }

    #[Test]
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
