<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Controller;
use Ouzo\Csrf\CsrfProtector;
use Ouzo\Exception\ForbiddenException;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use PHPUnit\Framework\Attributes\Test;

class CsrfSampleController extends Controller
{
    public function init()
    {
        CsrfProtector::protect($this);
    }

    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }

    public function modify()
    {
        $this->layout->renderAjax('modify');
        $this->layout->unsetLayout();
    }
}

class CsrfProtectorTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Route::$validate = false;
        Route::allowAll('/csrf_sample', CsrfSampleController::class);
    }

    public function tearDown(): void
    {
        Route::$validate = true;
        parent::tearDown();
    }

    #[Test]
    public function shouldFailIfNoCsrfCookie()
    {
        //when
        CatchException::when($this)->post('/csrf_sample/modify', []);

        //then
        CatchException::assertThat()->isInstanceOf(ForbiddenException::class);
    }

    #[Test]
    public function shouldFailIfNoCsrfToken()
    {
        //given
        $_SESSION = [];
        CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', []);

        //then
        CatchException::assertThat()->isInstanceOf(ForbiddenException::class);
    }

    #[Test]
    public function shouldFailIfInvalidCsrfToken()
    {
        //given
        $_SESSION = [];
        CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', ['csrftoken' => 'invalid']);

        //then
        CatchException::assertThat()->isInstanceOf(ForbiddenException::class);
    }

    #[Test]
    public function shouldFailIfInvalidCsrfAjaxHeader()
    {
        //given
        $_SESSION = [];
        CsrfProtector::getCsrfToken();
        $_SERVER['HTTP_X_CSRFTOKEN'] = 'invalid';

        //when
        CatchException::when($this)->post('/csrf_sample/modify', []);

        //then
        CatchException::assertThat()->isInstanceOf(ForbiddenException::class);
    }

    #[Test]
    public function shouldAcceptValidCsrfAjaxHeader()
    {
        //given
        $_SESSION = [];
        CsrfProtector::getCsrfToken();
        $_SERVER['HTTP_X_CSRFTOKEN'] = CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', []);

        //then
        CatchException::assertThat()->notCaught();
    }

    #[Test]
    public function shouldAcceptValidCsrfToken()
    {
        //given
        $_SESSION = [];
        CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', ['csrftoken' => CsrfProtector::getCsrfToken()]);

        //then
        CatchException::assertThat()->notCaught();
    }

    #[Test]
    public function shouldNotValidateGetMethod()
    {
        //when
        CatchException::when($this)->get('/csrf_sample/index');

        //then
        CatchException::assertThat()->notCaught();
    }
}
