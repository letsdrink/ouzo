<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Csrf\CsrfProtector;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;

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
    public function __construct()
    {
        Config::overrideProperty('namespace', 'controller')->with('\\');
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        Route::$validate = false;
        Route::allowAll('/csrf_sample', 'csrf_sample');
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
    public function shouldFailIfNoCsrfCookie()
    {
        //when
        CatchException::when($this)->post('/csrf_sample/modify', array());

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\ForbiddenException');
    }

    /**
     * @test
     */
    public function shouldFailIfInvalidCsrfCookie()
    {
        //
        $_SESSION = array();
        $_COOKIE['csrftoken'] = 'invalid';

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array('csrftoken' => CsrfProtector::getCsrfToken()));

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\ForbiddenException');
    }

    /**
     * @test
     */
    public function shouldFailIfNoCsrfToken()
    {
        //given
        $_SESSION = array();
        $_COOKIE['csrftoken'] = CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array());

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\ForbiddenException');
    }

    /**
     * @test
     */
    public function shouldFailIfInvalidCsrfToken()
    {
        //given
        $_SESSION = array();
        $_COOKIE['csrftoken'] = CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array('csrftoken' => 'invalid'));

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\ForbiddenException');
    }

    /**
     * @test
     */
    public function shouldFailIfInvalidCsrfAjaxHeader()
    {
        //given
        $_SESSION = array();
        $_COOKIE['csrftoken'] = CsrfProtector::getCsrfToken();
        $_SERVER['HTTP_X_CSRFTOKEN'] = 'invalid';

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array());

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Api\ForbiddenException');
    }

    /**
     * @test
     */
    public function shouldAcceptValidCsrfAjaxHeader()
    {
        //given
        $_SESSION = array();
        $_COOKIE['csrftoken'] = CsrfProtector::getCsrfToken();
        $_SERVER['HTTP_X_CSRFTOKEN'] = CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array());

        //then
        CatchException::assertThat()->notCaught();
    }

    /**
     * @test
     */
    public function shouldAcceptValidCsrfToken()
    {
        //given
        $_SESSION = array();
        $_COOKIE['csrftoken'] = CsrfProtector::getCsrfToken();

        //when
        CatchException::when($this)->post('/csrf_sample/modify', array('csrftoken' => CsrfProtector::getCsrfToken()));

        //then
        CatchException::assertThat()->notCaught();
    }

    /**
     * @test
     */
    public function shouldNotValidateGetMethod()
    {
        //when
        CatchException::when($this)->get('/csrf_sample/index');

        //then
        CatchException::assertThat()->notCaught();
    }

    /**
     * @test
     */
    public function shouldSetCookie()
    {
        //when
        $this->get('/csrf_sample/index');

        //then
        $this->assertHasCookie(array(
            'name' => 'csrftoken',
            'value' => CsrfProtector::getCsrfToken(),
            'expire' => 0,
            'path' => '/'
        ));
    }
}

